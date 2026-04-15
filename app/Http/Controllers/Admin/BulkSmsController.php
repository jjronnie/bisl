<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Jobs\BulkSmsJob;
use App\Jobs\SendSmsJob;
use App\Models\BulkSmsCampaign;
use App\Models\Member;
use App\Models\SmsLog;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class BulkSmsController extends Controller
{
    public function index(): View
    {
        $campaigns = BulkSmsCampaign::with('creator')
            ->latest()
            ->paginate(15);

        return view('admin.bulk-sms.index', compact('campaigns'));
    }

    public function create(): View
    {
        $members = Member::with('user')
            ->whereNull('deleted_at')
            ->orderBy('name')
            ->get();

        $activeCampaign = BulkSmsCampaign::whereIn('status', ['pending', 'processing'])
            ->latest()
            ->first();

        return view('admin.bulk-sms.create', compact('members', 'activeCampaign'));
    }

    public function show(int $id): View
    {
        $campaign = BulkSmsCampaign::with('creator')->findOrFail($id);

        $logs = SmsLog::with('recipient')
            ->where('bulk_sms_campaign_id', $campaign->id)
            ->orderBy('sent_at', 'desc')
            ->get();

        return view('admin.bulk-sms.show', compact('campaign', 'logs'));
    }

    public function status(int $id): JsonResponse
    {
        $campaign = BulkSmsCampaign::findOrFail($id);

        return response()->json([
            'id' => $campaign->id,
            'status' => $campaign->status,
            'total_recipients' => $campaign->total_recipients,
            'sent_count' => $campaign->sent_count,
            'failed_count' => $campaign->failed_count,
            'pending_count' => $campaign->total_recipients - $campaign->sent_count - $campaign->failed_count,
            'total_cost' => $campaign->total_cost,
            'progress' => $campaign->progressPercentage(),
            'started_at' => $campaign->started_at?->toIso8601String(),
            'completed_at' => $campaign->completed_at?->toIso8601String(),
        ]);
    }

    public function send(Request $request): RedirectResponse
    {
        $request->validate([
            'member_ids' => 'required|array|min:1',
            'member_ids.*' => 'exists:members,id',
            'message' => 'required|string|max:612',
        ]);

        $activeCampaign = BulkSmsCampaign::whereIn('status', ['pending', 'processing'])
            ->exists();

        if ($activeCampaign) {
            return redirect()
                ->back()
                ->with('error', 'A bulk SMS campaign is already in progress. Please wait for it to complete.');
        }

        $members = Member::whereIn('id', $request->member_ids)
            ->whereNotNull('phone1')
            ->get();

        if ($members->isEmpty()) {
            return redirect()
                ->back()
                ->with('error', 'No valid recipients found.');
        }

        $campaign = BulkSmsCampaign::create([
            'message' => $request->message,
            'total_recipients' => $members->count(),
            'status' => 'pending',
            'created_by' => auth()->id(),
        ]);

        BulkSmsJob::dispatch($campaign->id, $members->pluck('id')->toArray());

        return redirect()
            ->route('admin.bulk-sms.show', $campaign->id)
            ->with('info', 'Bulk SMS campaign started. This page will update as messages are sent.');
    }

    public function resend(int $id): RedirectResponse
    {
        $campaign = BulkSmsCampaign::with('smsLogs')->findOrFail($id);

        if (! $campaign->canResend()) {
            return redirect()
                ->back()
                ->with('error', 'This campaign cannot be resent. It may still be processing, is too new, or is older than 24 hours.');
        }

        $activeCampaign = BulkSmsCampaign::whereIn('status', ['pending', 'processing'])
            ->exists();

        if ($activeCampaign) {
            return redirect()
                ->back()
                ->with('error', 'A bulk SMS campaign is already in progress.');
        }

        $failedLogs = $campaign->getFailedOrPendingSmsLogs();

        if ($failedLogs->isEmpty()) {
            return redirect()
                ->back()
                ->with('error', 'No failed recipients found to resend.');
        }

        foreach ($failedLogs as $log) {
            $log->update([
                'status' => 'pending',
                'provider_status_code' => null,
                'provider_status_message' => null,
                'error_message' => null,
                'retry_count' => 0,
            ]);

            SendSmsJob::dispatch(
                $log->phone_number,
                $log->message,
                $log->notification_type,
                (string) $log->recipient_id,
                $log->id
            );
        }

        $campaign->update([
            'status' => 'processing',
            'started_at' => $campaign->started_at ?? now(),
        ]);

        return redirect()
            ->route('admin.bulk-sms.show', $campaign->id)
            ->with('info', 'Retrying failed messages. This page will update as messages are sent.');
    }

    public function cancel(int $id): JsonResponse
    {
        $campaign = BulkSmsCampaign::findOrFail($id);

        if (! $campaign->isProcessing() && ! $campaign->isPending()) {
            return response()->json([
                'success' => false,
                'message' => 'Cannot cancel this campaign.',
            ], 422);
        }

        $campaign->update(['status' => 'cancelled']);

        return response()->json([
            'success' => true,
            'message' => 'Campaign cancelled.',
        ]);
    }

    public function destroy(int $id): RedirectResponse
    {
        abort_unless(auth()->user()->hasRole('superadmin'), 403);

        $campaign = BulkSmsCampaign::findOrFail($id);

        $campaign->smsLogs()->delete();
        $campaign->delete();

        return redirect()
            ->route('admin.bulk-sms.index')
            ->with('success', 'Campaign deleted successfully.');
    }
}
