<?php

namespace App\Http\Resources;

use App\Models\Image;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin Image */
class ImageResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'           => $this->id,
            'path'         => $this->path,
            'preview_path' => $this->preview_path,
            'created_at'   => $this->created_at,

            'tags' => TagResource::collection($this->tags)
        ];
    }
}
