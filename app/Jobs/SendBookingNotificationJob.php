<?php

namespace App\Jobs;

use App\Models\BookingRequest;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class SendBookingNotificationJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(
        public BookingRequest $booking,
        public string $eventType, // 'created', 'approved', 'rejected', 'cancelled'
        public ?User $recipient = null
    ) {}

    public function handle(): void
    {
        $recipient = $this->recipient ?? match ($this->eventType) {
            'created' => $this->booking->unit->property->owner,
            default => $this->booking->tenant,
        };

        if (! $recipient) {
            return;
        }

        Log::info('SendBookingNotificationJob: Dispatched booking notification', [
            'booking_code' => $this->booking->code,
            'event' => $this->eventType,
            'recipient_id' => $recipient->id,
            'recipient_email' => $recipient->email,
        ]);
    }
}
