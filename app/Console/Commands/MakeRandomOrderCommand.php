<?php

namespace App\Console\Commands;

use App\Models\Image;
use Illuminate\Console\Command;

class MakeRandomOrderCommand extends Command
{
    protected $signature = 'make:random-order';

    protected $description = 'Maker random order.';

    public function handle(): void
    {
        Image::inRandomOrder()->get()->each(function (Image $image, int $index) {
            $image->order_id = $index + 1;
            $image->save();
        });
    }
}
