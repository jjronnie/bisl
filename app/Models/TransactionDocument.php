<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class TransactionDocument extends Model
{
    use HasFactory;

    protected $fillable = [
        'transaction_id',
        'name',
        'notes',
        'file_path',
    ];

    protected static function booted(): void
    {
        static::deleted(function (TransactionDocument $document) {
            if (Storage::exists($document->file_path)) {
                Storage::delete($document->file_path);
            }
        });
    }

    public function transaction(): BelongsTo
    {
        return $this->belongsTo(Transaction::class);
    }

    public function getUrlAttribute(): string
    {
        return Storage::url($this->file_path);
    }
}
