<?php

namespace App\Listeners;

use App\Events\ImageCreated;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Http;

class PredictTags implements ShouldQueue
{
    public string $queue = 'predictor';

    public function __construct()
    {
    }

    public function handle(ImageCreated $event): void
    {
        $response = Http::get(config('services.predictor.url'), [
            'url'       => config('app.url') . '/storage/' . $event->image->path,
            'threshold' => config('services.predictor.threshold')
        ])->json();

        foreach ($response['tags'] as $info) {
            $event->image->tags()->firstOrCreate([
                'name' => $info[0]
            ]);
        }

        if (!$event->image
            ->tags()
            ->whereIn('name', config('safety.bad_tags'))
            ->exists()) {
            $event->image->update(['is_safe' => true]);
        }
    }
}
