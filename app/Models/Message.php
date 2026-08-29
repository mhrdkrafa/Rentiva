<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class Message extends Model
{
    use HasFactory;

    protected $fillable = [
        'conversation_id',
        'sender_id',
        'body',
        'attachment_path',
        'attachment_mime',
        'attachment_size',
    ];

    public function conversation(): BelongsTo
    {
        return $this->belongsTo(Conversation::class);
    }

    public function sender(): BelongsTo
    {
        return $this->belongsTo(User::class, 'sender_id');
    }

    public function hasAttachment(): bool
    {
        return ! empty($this->attachment_path);
    }

    public function getAttachmentUrlAttribute(): ?string
    {
        if (! $this->attachment_path) {
            return null;
        }

        return Storage::disk('public')->url($this->attachment_path);
    }

    public function isImageAttachment(): bool
    {
        return in_array($this->attachment_mime, ['image/jpeg', 'image/png', 'image/webp', 'image/gif']);
    }

    public function getFormattedAttachmentSizeAttribute(): string
    {
        if (! $this->attachment_size) {
            return '0 B';
        }

        $units = ['B', 'KB', 'MB', 'GB'];
        $power = $this->attachment_size > 0 ? floor(log($this->attachment_size, 1024)) : 0;

        return number_format($this->attachment_size / pow(1024, $power), 1) . ' ' . ($units[$power] ?? 'B');
    }
}
