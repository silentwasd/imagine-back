<?php

namespace App\Models;

use App\Events\ImageCreated;
use App\Events\ImageCreating;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Image extends Model
{
    protected $fillable = [
        'path',
        'hash'
    ];

    protected $casts = [
        'is_safe' => 'boolean'
    ];

    protected $dispatchesEvents = [
        'creating' => ImageCreating::class,
        'created'  => ImageCreated::class
    ];

    public function tags(): BelongsToMany
    {
        return $this->belongsToMany(Tag::class);
    }
}
