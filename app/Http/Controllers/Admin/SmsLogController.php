<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SmsLog;
use Illuminate\Http\Request;

class SmsLogController extends Controller
{
    /**
     * Show SMS logs with statistics
     */
    public function index(Request $request)
    {
        // Get stats
        $sent = SmsLog::where('status', 'sent')->count();
        $total = SmsLog::count();

        $stats = [
            'sent' => $sent,
            'pending' => SmsLog::where('status', 'pending')->count(),
            'failed' => SmsLog::where('status', 'failed')->count(),
            'total' => $total,
            'total_cost' => SmsLog::sum('cost') ?? 0,
            'delivery_rate' => $total > 0 ? round(($sent / $total) * 100, 2) : 0,
        ];

        // Get logs with filtering
        $logs = SmsLog::query()
            ->when($request->filled('status'), fn ($q) => $q->where('status', $request->status))
            ->when($request->filled('notification_type'), fn ($q) => $q->where('notification_type', $request->notification_type))
            ->when($request->filled('search'), fn ($q) => $q->where('phone_number', 'like', '%'.$request->search.'%')
                ->orWhere('message', 'like', '%'.$request->search.'%'))
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return view('admin.sms-logs.index', compact('logs', 'stats'));
    }

    /**
     * Show SMS log details modal content
     */
    public function show(SmsLog $smsLog)
    {
        return view('admin.sms-logs.show', compact('smsLog'));
    }
}
