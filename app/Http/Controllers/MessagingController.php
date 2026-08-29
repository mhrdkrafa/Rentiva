<?php

namespace App\Http\Controllers;

use App\Actions\Messaging\MarkConversationReadAction;
use App\Actions\Messaging\SendMessageAction;
use App\Actions\Messaging\StartConversationAction;
use App\Models\Conversation;
use App\Models\Property;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class MessagingController extends Controller
{
    public function index(Request $request): View
    {
        $user = $request->user();

        $conversations = $user ? $user->conversations()
            ->with(['participants.profile', 'property.coverImage', 'latestMessage.sender'])
            ->get() : collect();

        $activeConversation = null;
        if ($request->filled('conversation')) {
            $activeConversation = Conversation::with(['participants.profile', 'property.coverImage'])->findOrFail((int) $request->conversation);

            // Check authorization
            if (! Gate::forUser($user)->allows('view', $activeConversation)) {
                abort(403, 'Akses percakapan tidak diizinkan.');
            }
        } elseif ($conversations->isNotEmpty()) {
            $activeConversation = $conversations->first();
        }

        $messages = collect();
        if ($activeConversation && $user) {
            // Mark as read
            app(MarkConversationReadAction::class)->execute($user, $activeConversation);

            $messages = $activeConversation->messages()
                ->with('sender.profile')
                ->oldest()
                ->get();
        }

        return view('messaging.index', compact('conversations', 'activeConversation', 'messages'));
    }

    public function show(Request $request, Conversation $conversation): View|RedirectResponse
    {
        if (! Gate::forUser($request->user())->allows('view', $conversation)) {
            abort(403, 'Akses percakapan tidak diizinkan.');
        }

        return redirect()->route('messages.index', ['conversation' => $conversation->id]);
    }

    public function send(Request $request, Conversation $conversation, SendMessageAction $action): RedirectResponse
    {
        if (! Gate::forUser($request->user())->allows('sendMessage', $conversation)) {
            abort(403, 'Akses percakapan tidak diizinkan.');
        }

        $validated = $request->validate([
            'body' => ['required', 'string', 'max:2000'],
            'attachment' => ['nullable', 'file', 'mimes:jpg,jpeg,png,webp,pdf', 'max:5120'],
        ]);

        $action->execute(
            $request->user(),
            $conversation,
            $validated['body'],
            $request->file('attachment')
        );

        return redirect()->route('messages.index', ['conversation' => $conversation->id])
            ->with('success', 'Pesan terkirim.');
    }

    public function start(Request $request, StartConversationAction $action): RedirectResponse
    {
        $user = $request->user();

        $validated = $request->validate([
            'property_id' => ['required', 'exists:properties,id'],
            'initial_message' => ['nullable', 'string', 'max:1000'],
        ]);

        $property = Property::with('owner')->findOrFail($validated['property_id']);
        $recipient = $property->owner;

        if ($recipient->id === $user->id) {
            return back()->with('error', 'Anda adalah pemilik properti ini.');
        }

        $initialMessage = $validated['initial_message'] ?? 'Halo, saya tertarik dengan ' . $property->name . '. Apakah kamar masih tersedia?';

        $conversation = $action->execute(
            initiator: $user,
            recipient: $recipient,
            property: $property,
            initialMessage: $initialMessage
        );

        return redirect()->route('messages.index', ['conversation' => $conversation->id]);
    }
}
