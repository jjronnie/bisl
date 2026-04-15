<?php

namespace App\Jobs;

use AfricasTalking\SDK\AfricasTalking;
use App\Helpers\PhoneHelper;
use App\Models\BulkSmsCampaign;
use App\Models\SmsLog;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class SendSmsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $tries = 3;

    public $backoff = [60, 120, 300];

    public $timeout = 30;

    protected string $phoneNumber;

    protected string $message;

    protected string $notificationType;

    protected ?string $recipientId;

    protected ?int $smsLogId;

    public function __construct(
        string $phoneNumber,
        string $message,
        string $notificationType,
        ?string $recipientId = null,
        ?int $smsLogId = null
    ) {
        $this->phoneNumber = PhoneHelper::normalize($phoneNumber);
        $this->message = $message;
        $this->notificationType = $notificationType;
        $this->recipientId = $recipientId;
        $this->smsLogId = $smsLogId;
    }

    public function handle(): void
    {
        try {
            if (! PhoneHelper::isValid($this->phoneNumber)) {
                throw new \Exception('Invalid phone number: '.$this->phoneNumber);
            }

            $at = new AfricasTalking(
                config('services.africas_talking.username'),
                config('services.africas_talking.api_key')
            );

            $sms = $at->sms();

            $result = $sms->send([
                'to' => $this->phoneNumber,
                'message' => $this->message,
            ]);

            $cost = $this->extractCost($result);
            $providerStatus = $this->extractProviderStatus($result);
            $isSuccess = $this->isProviderSuccess($providerStatus);

            $this->updateOrCreateLog([
                'phone_number' => $this->phoneNumber,
                'message' => $this->message,
                'notification_type' => $this->notificationType,
                'recipient_id' => $this->recipientId,
                'status' => $isSuccess ? 'sent' : 'failed',
                'provider_status_code' => $providerStatus['code'] ?? null,
                'provider_status_message' => $providerStatus['message'] ?? null,
                'provider_response' => (array) $result,
                'sent_at' => $isSuccess ? now() : null,
                'retry_count' => $this->attempts() - 1,
                'cost' => $isSuccess ? $cost : null,
                'error_message' => $isSuccess ? null : ($providerStatus['message'] ?? 'Provider reported failure'),
            ]);

            Log::info('SMS sent', [
                'phone' => $this->phoneNumber,
                'type' => $this->notificationType,
                'log_id' => $this->smsLogId,
                'provider_status' => $providerStatus['code'] ?? 'unknown',
                'success' => $isSuccess,
            ]);

        } catch (\Exception $e) {
            $retryCount = $this->attempts();
            $willRetry = $retryCount < $this->tries;

            $this->updateOrCreateLog([
                'phone_number' => $this->phoneNumber,
                'message' => $this->message,
                'notification_type' => $this->notificationType,
                'recipient_id' => $this->recipientId,
                'status' => $willRetry ? 'pending' : 'failed',
                'retry_count' => $retryCount,
                'error_message' => $e->getMessage(),
                'sent_at' => null,
            ]);

            Log::error('SMS sending failed', [
                'phone' => $this->phoneNumber,
                'error' => $e->getMessage(),
                'attempt' => $retryCount,
                'will_retry' => $willRetry,
            ]);

            if ($willRetry) {
                $this->release($this->backoff[$retryCount - 1] ?? 300);
            } else {
                throw $e;
            }
        }
    }

    public function failed(\Throwable $exception): void
    {
        Log::critical('SMS job failed after all retries', [
            'phone' => $this->phoneNumber,
            'error' => $exception->getMessage(),
            'type' => $this->notificationType,
        ]);

        if ($this->smsLogId) {
            $log = SmsLog::find($this->smsLogId);
            if ($log) {
                $log->update(['status' => 'failed']);
                $this->updateBulkCampaignCounts($log->bulk_sms_campaign_id);
            }
        }
    }

    protected function updateOrCreateLog(array $data): void
    {
        if ($this->smsLogId) {
            $log = SmsLog::find($this->smsLogId);
            if ($log) {
                $log->update($data);
                $this->smsLogId = $log->id;
            }
        } else {
            $log = SmsLog::create($data);
            $this->smsLogId = $log->id;
        }

        $this->updateBulkCampaignCounts($log->bulk_sms_campaign_id);
    }

    protected function updateBulkCampaignCounts(?int $campaignId): void
    {
        if (! $campaignId) {
            return;
        }

        $campaign = BulkSmsCampaign::find($campaignId);
        if (! $campaign) {
            return;
        }

        $logs = SmsLog::where('bulk_sms_campaign_id', $campaignId)->get();

        $successCodes = SmsLog::PROVIDER_SUCCESS_CODES;
        $failureCodes = SmsLog::PROVIDER_FAILURE_CODES;

        $sentCount = $logs->filter(function ($log) use ($successCodes) {
            return $log->provider_status_code && in_array($log->provider_status_code, $successCodes);
        })->count();

        $failedCount = $logs->filter(function ($log) use ($successCodes, $failureCodes) {
            return $log->provider_status_code
                && ! in_array($log->provider_status_code, $successCodes)
                && in_array($log->provider_status_code, $failureCodes);
        })->count();

        $pendingCount = $logs->filter(function ($log) use ($successCodes, $failureCodes) {
            return ! $log->provider_status_code
                || (! in_array($log->provider_status_code, $successCodes) && ! in_array($log->provider_status_code, $failureCodes));
        })->count();

        $totalCost = $logs->where('status', 'sent')->sum('cost');

        $updateData = [
            'sent_count' => $sentCount,
            'failed_count' => $failedCount,
            'total_cost' => $totalCost,
        ];

        if ($pendingCount === 0 && $campaign->status !== 'cancelled') {
            $updateData['status'] = 'completed';
            $updateData['completed_at'] = now();
        }

        $campaign->update($updateData);
    }

    protected function extractCost($result): ?float
    {
        try {
            $recipients = data_get($result, 'data.SMSMessageData.Recipients.0');
            if (! $recipients) {
                return null;
            }

            $recipients = (array) $recipients;
            $costString = $recipients['cost'] ?? null;
            if (! $costString) {
                return null;
            }

            preg_match('/[\d.]+/', $costString, $matches);

            return $matches ? (float) $matches[0] : null;
        } catch (\Exception $e) {
            Log::warning('Failed to extract SMS cost', ['error' => $e->getMessage()]);

            return null;
        }
    }

    protected function extractProviderStatus($result): array
    {
        try {
            $recipients = data_get($result, 'data.SMSMessageData.Recipients.0');
            if (! $recipients) {
                return ['code' => null, 'message' => 'No recipient data'];
            }

            $recipients = (array) $recipients;

            return [
                'code' => (string) ($recipients['statusCode'] ?? $recipients['status'] ?? null),
                'message' => $recipients['status'] ?? $recipients['statusMessage'] ?? null,
            ];
        } catch (\Exception $e) {
            Log::warning('Failed to extract provider status', ['error' => $e->getMessage()]);

            return ['code' => null, 'message' => $e->getMessage()];
        }
    }

    protected function isProviderSuccess(array $status): bool
    {
        $code = $status['code'] ?? null;
        if (! $code) {
            return false;
        }

        return in_array($code, ['100', '101', '102']);
    }
}
