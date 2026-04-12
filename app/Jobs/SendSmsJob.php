<?php

namespace App\Jobs;

use App\Helpers\PhoneHelper;
use App\Models\SmsLog;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use AfricasTalking\SDK\AfricasTalking;

class SendSmsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $tries = 3;
    public $backoff = [60, 120, 300]; // Retry after 1m, 2m, 5m
    public $timeout = 30;

    protected $phoneNumber;
    protected $message;
    protected $notificationType;
    protected $recipientId;
    protected $smsLogId;

    /**
     * Create a new job instance.
     */
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

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        try {
            // Validate phone number
            if (!PhoneHelper::isValid($this->phoneNumber)) {
                throw new \Exception('Invalid phone number: ' . $this->phoneNumber);
            }

            // Initialize Africa's Talking SDK
            $at = new AfricasTalking(
                config('services.africas_talking.username'),
                config('services.africas_talking.api_key')
            );

            $sms = $at->sms();

            // Send SMS
            $result = $sms->send([
                'to' => $this->phoneNumber,
                'message' => $this->message
            ]);

            // Extract cost from provider response
            $cost = $this->extractCost($result);

            // Create or update SMS log
            $log = $this->smsLogId
                ? SmsLog::find($this->smsLogId)
                : new SmsLog();

            $log->fill([
                'phone_number' => $this->phoneNumber,
                'message' => $this->message,
                'notification_type' => $this->notificationType,
                'recipient_id' => $this->recipientId,
                'status' => 'sent',
                'provider_response' => (array) $result,
                'sent_at' => now(),
                'retry_count' => $this->attempts() - 1,
                'cost' => $cost,
            ])->save();

            Log::info('SMS sent successfully', [
                'phone' => $this->phoneNumber,
                'type' => $this->notificationType,
                'log_id' => $log->id,
            ]);

        } catch (\Exception $e) {
            // Create or update error log
            $log = $this->smsLogId
                ? SmsLog::find($this->smsLogId)
                : new SmsLog();

            $log->fill([
                'phone_number' => $this->phoneNumber,
                'message' => $this->message,
                'notification_type' => $this->notificationType,
                'recipient_id' => $this->recipientId,
                'status' => 'failed',
                'error_message' => $e->getMessage(),
                'retry_count' => $this->attempts(),
            ])->save();

            Log::error('SMS sending failed', [
                'phone' => $this->phoneNumber,
                'error' => $e->getMessage(),
                'attempt' => $this->attempts(),
            ]);

            // Retry or mark as finally failed
            if ($this->attempts() < $this->tries) {
                $this->smsLogId = $log->id;
                $this->release($this->backoff[$this->attempts() - 1] ?? 300);
            } else {
                throw $e;
            }
        }
    }

    /**
     * Handle job failure
     */
    public function failed(\Throwable $exception): void
    {
        Log::critical('SMS job failed after all retries', [
            'phone' => $this->phoneNumber,
            'error' => $exception->getMessage(),
            'type' => $this->notificationType,
        ]);

        // Update log to mark as permanently failed
        if ($this->smsLogId) {
            $log = SmsLog::find($this->smsLogId);
            if ($log) {
                $log->update(['status' => 'failed']);
            }
        }
    }

    /**
     * Extract numeric cost from provider response
     * Cost format: "UGX 35.0000" -> 35.0000
     */
    protected function extractCost($result): ?float
    {
        try {
            $recipients = data_get($result, 'data.SMSMessageData.Recipients.0');
            if (!$recipients) {
                return null;
            }

            // Convert object to array if needed
            $recipients = (array) $recipients;
            $costString = $recipients['cost'] ?? null;
            if (!$costString) {
                return null;
            }

            // Extract numeric value from "UGX 35.0000"
            preg_match('/[\d.]+/', $costString, $matches);
            return $matches ? (float) $matches[0] : null;
        } catch (\Exception $e) {
            Log::warning('Failed to extract SMS cost', ['error' => $e->getMessage()]);
            return null;
        }
    }
}
