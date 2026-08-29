<?php

use App\Support\MediaStorage;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

test('media storage returns public url or placeholder', function () {
    expect(MediaStorage::publicUrl(null))->toContain('placeholders/property.jpg')
        ->and(MediaStorage::publicUrl('https://example.com/photo.jpg'))->toBe('https://example.com/photo.jpg');
});

test('media storage stores public image and private document', function () {
    Storage::fake('public');
    Storage::fake('local');

    $fakeImage = UploadedFile::fake()->create('room.jpg', 100, 'image/jpeg');
    $imagePath = MediaStorage::storePublicImage($fakeImage, 'properties');

    expect(Storage::disk('public')->exists($imagePath))->toBeTrue();

    $fakeDoc = UploadedFile::fake()->create('contract.pdf', 100, 'application/pdf');
    $docPath = MediaStorage::storePrivateDocument($fakeDoc, 'contracts');

    expect(Storage::disk('local')->exists($docPath))->toBeTrue();
});
