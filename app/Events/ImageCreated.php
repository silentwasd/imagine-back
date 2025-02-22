<?php

namespace App\Events;

use App\Models\Image;
use Illuminate\Foundation\Events\Dispatchable;

class ImageCreated
{
    use Dispatchable;

    public function __construct(
        public Image $image
    )
    {
    }
}
