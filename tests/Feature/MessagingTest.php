<?php

use App\Actions\Messaging\MarkConversationReadAction;
use App\Actions\Messaging\SendMessageAction;
use App\Actions\Messaging\StartConversationAction;
use App\Enums\PropertyStatus;
use App\Enums\VerificationStatus;
use App\Models\Conversation;
use App\Models\Location;
use App\Models\Message;
use App\Models\Property;
use App\Models\PropertyType;
use App\Models\User;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use InvalidArgumentException;

beforeEach(function () {
    Storage::fake('public');

    $this->owner = User::factory()->owner()->create();
    $this->tenant = User::factory()->tenant()->create();
    $this->otherTenant = User::factory()->tenant()->create();

    $this->location = Location::create(['name' => 'Kecamatan Gondokusuman, Yogyakarta', 'slug' => 'gondokusuman-jogja']);
    $this->propertyType = PropertyType::create(['name' => 'Kost Putri', 'slug' => 'kost-putri']);

    $this->property = Property::create([
        'owner_id' => $this->owner->id,
        'property_type_id' => $this->propertyType->id,
        'location_id' => $this->location->id,
        'name' => 'Kost Putri Sagan',
        'slug' => 'kost-putri-sagan',
        'description' => 'Kost putri asri dan strategis',
        'address' => 'Jl. Sagan No. 8',
        'verification_status' => VerificationStatus::VERIFIED,
        'status' => PropertyStatus::PUBLISHED,
        'published_at' => now(),
    ]);
});

test('tenant can start a conversation with property owner and exchange messages', function () {
    $startAction = new StartConversationAction();
    $conversation = $startAction->execute(
        initiator: $this->tenant,
        recipient: $this->owner,
        property: $this->property,
        initialMessage: 'Halo, apakah ada kamar kosong untuk bulan depan?'
    );

    expect($conversation)->toBeInstanceOf(Conversation::class)
        ->and($conversation->property_id)->toBe($this->property->id)
        ->and($conversation->participants)->toHaveCount(2)
        ->and($conversation->messages)->toHaveCount(1)
        ->and($conversation->messages->first()->body)->toBe('Halo, apakah ada kamar kosong untuk bulan depan?');

    // Owner replies
    $sendAction = new SendMessageAction();
    $reply = $sendAction->execute(
        sender: $this->owner,
        conversation: $conversation,
        body: 'Halo, masih ada 2 kamar kosong tipe standar.'
    );

    expect($reply)->toBeInstanceOf(Message::class)
        ->and($reply->sender_id)->toBe($this->owner->id)
        ->and($conversation->fresh()->last_message_at)->not->toBeNull();
});

test('message attachment is validated and stored with correct metadata', function () {
    $startAction = new StartConversationAction();
    $conversation = $startAction->execute($this->tenant, $this->owner, $this->property);

    $sendAction = new SendMessageAction();

    // 1. Valid image attachment
    $file = UploadedFile::fake()->create('ktp_preview.jpg', 50, 'image/jpeg');
    $msgWithImage = $sendAction->execute($this->tenant, $conversation, 'Berikut lampiran foto KTP saya', $file);

    expect($msgWithImage->hasAttachment())->toBeTrue()
        ->and($msgWithImage->isImageAttachment())->toBeTrue()
        ->and($msgWithImage->attachment_mime)->toBe('image/jpeg')
        ->and(Storage::disk('public')->exists($msgWithImage->attachment_path))->toBeTrue();

    // 2. Disallowed file type (.exe / .zip) -> throws InvalidArgumentException
    $badFile = UploadedFile::fake()->create('malicious.exe', 500, 'application/x-msdownload');
    expect(fn () => $sendAction->execute($this->tenant, $conversation, 'File berbahaya', $badFile))
        ->toThrow(InvalidArgumentException::class);
});

test('conversation read receipts track unread status and count correctly', function () {
    $startAction = new StartConversationAction();
    $conversation = $startAction->execute($this->tenant, $this->owner, $this->property, null, 'Pesan baru');

    // Tenant is sender -> read
    expect($conversation->isUnreadFor($this->tenant))->toBeFalse()
        ->and($conversation->isUnreadFor($this->owner))->toBeTrue()
        ->and($this->owner->unreadConversationsCount())->toBe(1);

    // Owner opens chat and marks as read
    $markAction = new MarkConversationReadAction();
    $markAction->execute($this->owner, $conversation);

    expect($conversation->fresh()->isUnreadFor($this->owner))->toBeFalse()
        ->and($this->owner->fresh()->unreadConversationsCount())->toBe(0);
});

test('messaging inbox web routes render conversation stream and post messages', function () {
    $startAction = new StartConversationAction();
    $conversation = $startAction->execute($this->tenant, $this->owner, $this->property, null, 'Halo dari inbox');

    // Tenant views inbox
    $response = $this->actingAs($this->tenant)->get(route('messages.index', ['conversation' => $conversation->id]));
    $response->assertOk()
        ->assertSee('Kotak Masuk Pesan')
        ->assertSee('Halo dari inbox')
        ->assertSee('Kost Putri Sagan');

    // Tenant posts a message via HTTP
    $postResponse = $this->actingAs($this->tenant)->post(route('messages.send', $conversation), [
        'body' => 'Berapa biaya depositnya ya?',
    ]);
    $postResponse->assertRedirect(route('messages.index', ['conversation' => $conversation->id]));

    expect($conversation->fresh()->messages)->toHaveCount(2);
});

test('unauthorized non-participant cannot view or send messages in a conversation', function () {
    $startAction = new StartConversationAction();
    $conversation = $startAction->execute($this->tenant, $this->owner, $this->property, null, 'Pesan rahasia');

    // Other tenant attempts to view inbox on this conversation -> 403
    $this->actingAs($this->otherTenant)
        ->get(route('messages.index', ['conversation' => $conversation->id]))
        ->assertForbidden();

    // Other tenant attempts to send message -> 403 / AuthorizationException
    $sendAction = new SendMessageAction();
    expect(fn () => $sendAction->execute($this->otherTenant, $conversation, 'Penyusup'))
        ->toThrow(AuthorizationException::class);
});
