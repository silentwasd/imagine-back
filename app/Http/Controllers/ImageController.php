<?php

namespace App\Http\Controllers;

use App\Http\Resources\ImageResource;
use App\Models\Image;
use Exception;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ImageController extends Controller
{
    public function index(Request $request)
    {
        $data = $request->validate([
            'tags'             => 'nullable|array',
            'tags.*'           => 'integer|exists:tags,id',
            'image_id'         => 'nullable|integer|exists:images,id',
            'exclude_image_id' => 'nullable|integer|exists:images,id',
            'mature'           => 'nullable|boolean'
        ]);

        $images = Image::query()
                       ->when($data['tags'] ?? false, fn($when) => $when
                           ->whereHas('tags', fn(Builder $has) => $has
                               ->whereIn('image_tag.tag_id', $data['tags'])
                               , '=', count($data['tags']))
                       )
                       ->with(['tags' => fn($with) => $with->withCount('images')->orderByDesc('images_count')])
                       ->when($data['image_id'] ?? false, fn($when) => $when->orderByRaw('id = ? DESC', [$data['image_id']]))
                       ->when($data['exclude_image_id'] ?? false, fn($when) => $when->whereNot('id', $data['exclude_image_id']))
                       ->when(!($data['mature'] ?? false), fn($when) => $when->whereDoesntHave('tags', fn(Builder $has) => $has
                           ->whereIn('tags.name', [
                               'rating:explicit',
                               'rating:questionable',
                               'pussy',
                               'panties',
                               'butt_crack',
                               'nipples',
                               'nude',
                               'sex_toy',
                               'object_insertion',
                               'anal_tail',
                               'anal_beads',
                               'anal_object_insertion',
                               'hetero',
                               'clothes_lift',
                               'lifted_by_self',
                               'cum',
                               'sex',
                               'bottomless'
                           ])
                       )->has('tags'))
                       ->orderBy('order_id')
                       ->paginate(perPage: 8 * 10);

        return ImageResource::collection($images);
    }

    public function store(Request $request)
    {
        $request->validate([
            'image' => 'required|image|max:10000'
        ]);

        $path = $request->file('image')->store('images', 'public');

        try {
            $image = Image::create(['path' => $path]);
        } catch (Exception $e) {
            Log::info($e->getTraceAsString());;
            abort(400, $e->getMessage());
        }

        return new ImageResource($image);
    }

    public function show(Image $image)
    {
        $image->tags = $image->tags()
                             ->withCount('images')
                             ->orderByDesc('images_count')
                             ->orderBy('id')
                             ->get();

        return new ImageResource($image);
    }
}
