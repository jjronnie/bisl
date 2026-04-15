<?php

namespace App\Jobs;

use App\Helpers\MessageHelper;
use App\Helpers\PhoneHelper;
use App\Models\BulkSmsCampaign;
use App\Models\Member;
use App\Models\SmsLog;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class BulkSmsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $tries = 1;

    public $timeout = 300;

    protected $campaignId;

    protected $recipientIds;

    public function __construct(int $campaignId, array $recipientIds)
    {
        $this->campaignId = $campaignId;
        $this->recipientIds = $recipientIds;
    }

    public function handle(): void
    {
        $campaign = BulkSmsCampaign::find($this->campaignId);

        if (! $campaign) {
            Log::error('Bulk SMS campaign not found', ['campaign_id' => $this->campaignId]);

            return;
        }

        if ($campaign->status === 'cancelled') {
            Log::info('Bulk SMS campaign was cancelled', ['campaign_id' => $this->campaignId]);

            return;
        }

        $campaign->update([
            'status' => 'processing',
            'started_at' => now(),
        ]);

        $validRecipients = $this->prepareRecipients();

        if (empty($validRecipients)) {
            $campaign->update([
                'status' => 'completed',
                'completed_at' => now(),
            ]);

            return;
        }

        foreach ($validRecipients as $member) {
            if ($campaign->status === 'cancelled') {
                break;
            }

            $this->sendIndividualSms($campaign, $member);
        }

        $this->updateCampaignStatus($campaign);
    }

    protected function prepareRecipients(): array
    {
        $recipients = [];

        foreach ($this->recipientIds as $recipientId) {
            $member = Member::with('user')->find($recipientId);

            if (! $member || ! $member->phone1) {
                continue;
            }

            $normalized = PhoneHelper::normalize($member->phone1);

            if (! PhoneHelper::isValid($normalized)) {
                continue;
            }

            $recipients[$normalized] = $member;
        }

        return $recipients;
    }

    protected function sendIndividualSms(BulkSmsCampaign $campaign, Member $member): void
    {
        $phoneNumber = PhoneHelper::normalize($member->phone1);
        $recipientId = $member->user_id ?? $member->id;
        $personalizedMessage = MessageHelper::replacePlaceholders($campaign->message, $member);

        $log = SmsLog::create([
            'phone_number' => $phoneNumber,
            'message' => $personalizedMessage,
            'notification_type' => 'bulk_sms',
            'recipient_id' => $recipientId,
            'bulk_sms_campaign_id' => $campaign->id,
            'status' => 'pending',
        ]);

        try {
            $sendSmsJob = new SendSmsJob(
                $phoneNumber,
                $personalizedMessage,
                'bulk_sms',
                (string) $recipientId,
                $log->id
            );

            dispatch($sendSmsJob);

            Log::info('Bulk SMS dispatched for recipient', [
                'campaign_id' => $campaign->id,
                'phone' => $phoneNumber,
                'log_id' => $log->id,
            ]);
        } catch (\Exception $e) {
            $log->update([
                'status' => 'failed',
                'error_message' => $e->getMessage(),
            ]);

            Log::error('Failed to dispatch bulk SMS', [
                'campaign_id' => $campaign->id,
                'phone' => $phoneNumber,
                'error' => $e->getMessage(),
            ]);
        }
    }

    protected function updateCampaignStatus(BulkSmsCampaign $campaign): void
    {
        $campaign->refresh();

        $logs = SmsLog::where('bulk_sms_campaign_id', $campaign->id)->get();
        $sentCount = $logs->where('status', 'sent')->count();
        $failedCount = $logs->where('status', 'failed')->count();
        $totalCost = $logs->whereNotNull('cost')->sum('cost');
        $pendingCount = $logs->where('status', 'pending')->count();

        if ($pendingCount > 0) {
            return;
        }

        $campaign->update([
            'sent_count' => $sentCount,
            'failed_count' => $failedCount,
            'total_cost' => $totalCost,
            'status' => 'completed',
            'completed_at' => now(),
        ]);

        Log::info('Bulk SMS campaign completed', [
            'campaign_id' => $campaign->id,
            'sent' => $sentCount,
            'failed' => $failedCount,
            'cost' => $totalCost,
        ]);
    }

    public function failed(\Throwable $exception): void
    {
        $campaign = BulkSmsCampaign::find($this->campaignId);

        if ($campaign) {
            $campaign->update([
                'status' => 'completed',
                'failed_count' => count($this->recipientIds),
                'completed_at' => now(),
            ]);
        }

        Log::critical('Bulk SMS job failed', [
            'campaign_id' => $this->campaignId,
            'error' => $exception->getMessage(),
        ]);
    }
}
