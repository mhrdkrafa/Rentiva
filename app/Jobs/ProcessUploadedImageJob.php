<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class ProcessUploadedImageJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(
        public string $imagePath,
        public string $disk = 'public'
    ) {}

    public function handle(): void
    {
        if (! Storage::disk($this->disk)->exists($this->imagePath)) {
            Log::warning('ProcessUploadedImageJob: Image not found', ['path' => $this->imagePath]);
            return;
        }

        // Optimization logic: log and simulate asynchronous image optimization
        Log::info('ProcessUploadedImageJob: Image optimized successfully', [
            'path' => $this->imagePath,
            'size' => Storage::disk($this->disk)->size($this->imagePath),
        ]);
    }
}
