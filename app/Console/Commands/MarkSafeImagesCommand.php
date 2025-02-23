<?php

namespace App\Console\Commands;

use App\Models\Image;
use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Builder;

class MarkSafeImagesCommand extends Command
{
    protected $signature = 'mark:safe-images';

    protected $description = 'Mark safe images.';

    public function handle(): void
    {
        Image::query()->update(['is_safe' => true]);

        Image::whereHas('tags', fn(Builder $has) => $has
            ->whereIn('tags.name', config('safety.bad_tags'))
        )->update(['is_safe' => false]);
    }
}
