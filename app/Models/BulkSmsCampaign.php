<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class BulkSmsCampaign extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'message',
        'total_recipients',
        'sent_count',
        'failed_count',
        'total_cost',
        'status',
        'created_by',
        'started_at',
        'completed_at',
    ];

    protected $casts = [
        'total_cost' => 'decimal:4',
        'started_at' => 'datetime',
        'completed_at' => 'datetime',
    ];

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function smsLogs(): HasMany
    {
        return $this->hasMany(SmsLog::class);
    }

    public function isProcessing(): bool
    {
        return $this->status === 'processing';
    }

    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    public function isCompleted(): bool
    {
        return $this->status === 'completed';
    }

    public function progressPercentage(): float
    {
        if ($this->total_recipients === 0) {
            return 0;
        }

        return round(($this->sent_count + $this->failed_count) / $this->total_recipients * 100, 1);
    }

    public function getPendingCount(): int
    {
        return $this->total_recipients - $this->sent_count - $this->failed_count;
    }

    public function canResend(): bool
    {
        if (! $this->isCompleted()) {
            return false;
        }

        if ($this->created_at->diffInMinutes(now()) < 6) {
            return false;
        }

        if ($this->created_at->diffInHours(now()) >= 24) {
            return false;
        }

        $failedOrPending = $this->smsLogs()
            ->where(function ($query) {
                $query->whereIn('status', ['pending', 'failed'])
                    ->orWhere(function ($q) {
                        $q->where('status', 'sent')
                            ->whereNotIn('provider_status_code', SmsLog::PROVIDER_SUCCESS_CODES);
                    });
            })
            ->exists();

        return $failedOrPending;
    }

    public function getFailedOrPendingRecipients(): array
    {
        return $this->smsLogs()
            ->where(function ($query) {
                $query->whereIn('status', ['pending', 'failed'])
                    ->orWhere(function ($q) {
                        $q->where('status', 'sent')
                            ->whereNotIn('provider_status_code', SmsLog::PROVIDER_SUCCESS_CODES);
                    });
            })
            ->pluck('recipient_id')
            ->filter()
            ->unique()
            ->values()
            ->toArray();
    }

    public function getFailedOrPendingSmsLogs()
    {
        return $this->smsLogs()
            ->where(function ($query) {
                $query->whereIn('status', ['pending', 'failed'])
                    ->orWhere(function ($q) {
                        $q->where('status', 'sent')
                            ->whereNotIn('provider_status_code', SmsLog::PROVIDER_SUCCESS_CODES);
                    });
            })
            ->get();
    }
}
