<?php

namespace App\Actions\Messaging;

use App\Models\Conversation;
use App\Models\Message;
use App\Models\User;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use InvalidArgumentException;

class SendMessageAction
{
    /**
     * Allowed MIME types for message attachments.
     */
    protected array $allowedMimes = [
        'image/jpeg',
        'image/png',
        'image/webp',
        'application/pdf',
    ];

    /**
     * 5 Megabytes maximum attachment size.
     */
    protected int $maxSizeBytes = 5 * 1024 * 1024;

    public function execute(
        User $sender,
        Conversation $conversation,
        string $body,
        ?UploadedFile $attachment = null
    ): Message {
        // Authorize that sender is a participant
        if (! $conversation->participants()->where('users.id', $sender->id)->exists() && ! $sender->isAdmin()) {
            throw new AuthorizationException('Anda bukan peserta dalam percakapan ini.');
        }

        $attachmentPath = null;
        $attachmentMime = null;
        $attachmentSize = null;

        if ($attachment) {
            $mime = $attachment->getMimeType();
            $size = $attachment->getSize();

            if (! in_array($mime, $this->allowedMimes, true)) {
                throw new InvalidArgumentException('Format file lampiran tidak diizinkan. Gunakan JPG, PNG, WEBP, atau PDF.');
            }

            if ($size > $this->maxSizeBytes) {
                throw new InvalidArgumentException('Ukuran file lampiran maksimal adalah 5MB.');
            }

            $attachmentPath = $attachment->store('chat-attachments/' . $conversation->id, 'public');
            $attachmentMime = $mime;
            $attachmentSize = $size;
        }

        return DB::transaction(function () use ($sender, $conversation, $body, $attachmentPath, $attachmentMime, $attachmentSize) {
            $message = Message::create([
                'conversation_id' => $conversation->id,
                'sender_id' => $sender->id,
                'body' => trim($body),
                'attachment_path' => $attachmentPath,
                'attachment_mime' => $attachmentMime,
                'attachment_size' => $attachmentSize,
            ]);

            // Update conversation last_message_at
            $conversation->update([
                'last_message_at' => now(),
            ]);

            // Update sender's last_read_at
            $conversation->participants()->updateExistingPivot($sender->id, [
                'last_read_at' => now(),
            ]);

            return $message;
        });
    }
}
