<?php

namespace App\Console\Commands;

use App\Models\Tag;
use Illuminate\Console\Command;

class MakeFrequencyForTagsCommand extends Command
{
    protected $signature = 'make:frequency-for-tags';

    protected $description = 'Make frequency for tags.';

    public function handle(): void
    {
        foreach (Tag::withCount('images')->get() as $tag) {
            $tag->frequency = $tag->images_count;
            $tag->save();
        }
    }
}
