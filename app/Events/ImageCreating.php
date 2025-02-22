<?php

namespace App\Events;

use App\Models\Image;
use Illuminate\Foundation\Events\Dispatchable;

class ImageCreating
{
    use Dispatchable;

    public function __construct(
        public Image $image
    )
    {
    }
}
