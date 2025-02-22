<?php

namespace App\Listeners;

use App\Events\ImageCreating;
use App\Services\ImageService;
use Exception;
use Illuminate\Support\Facades\Storage;

class ComputeHash
{
    public function __construct(
        public ImageService $imageService
    )
    {
    }

    /**
     * @throws Exception
     */
    public function handle(ImageCreating $event): void
    {
        $path = Storage::disk('public')->path($event->image->path);

        $event->image->hash = $this->imageService->pHash($path);
    }
}
