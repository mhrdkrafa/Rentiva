<?php

namespace App\Actions\Messaging;

use App\Models\BookingRequest;
use App\Models\Conversation;
use App\Models\Property;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;

class StartConversationAction
{
    public function execute(
        User $initiator,
        User $recipient,
        ?Property $property = null,
        ?BookingRequest $bookingRequest = null,
        ?string $initialMessage = null
    ): Conversation {
        if ($initiator->id === $recipient->id) {
            throw new InvalidArgumentException('Anda tidak dapat memulai obrolan dengan diri sendiri.');
        }

        return DB::transaction(function () use ($initiator, $recipient, $property, $bookingRequest, $initialMessage) {
            // Check if there is an existing conversation between these 2 users (optionally matching property)
            $existing = Conversation::whereHas('participants', fn ($q) => $q->where('users.id', $initiator->id))
                ->whereHas('participants', fn ($q) => $q->where('users.id', $recipient->id))
                ->when($property, fn ($q) => $q->where('property_id', $property->id))
                ->first();

            if ($existing) {
                if (! empty($initialMessage)) {
                    app(SendMessageAction::class)->execute($initiator, $existing, $initialMessage);
                }

                return $existing->fresh(['participants', 'property', 'latestMessage']);
            }

            // Create new conversation
            $title = $property
                ? 'Tanya: ' . $property->name
                : 'Pesan Antara ' . $initiator->name . ' & ' . $recipient->name;

            $conversation = Conversation::create([
                'property_id' => $property?->id,
                'booking_request_id' => $bookingRequest?->id,
                'title' => $title,
                'last_message_at' => now(),
            ]);

            // Attach participants
            $conversation->participants()->attach([
                $initiator->id => ['last_read_at' => now()],
                $recipient->id => ['last_read_at' => null],
            ]);

            if (! empty($initialMessage)) {
                app(SendMessageAction::class)->execute($initiator, $conversation, $initialMessage);
            }

            return $conversation->fresh(['participants', 'property', 'latestMessage']);
        });
    }
}
