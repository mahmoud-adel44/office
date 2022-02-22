<?php

namespace App\Http\Resources;

use App\Models\Office;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use JetBrains\PhpStorm\ArrayShape;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

/** @mixin Office */
class OfficeResource extends JsonResource
{
    /**
     * @param Request $request
     * @return array
     */
    #[ArrayShape(['user' => "mixed", 'images' => AnonymousResourceCollection::class, 'tags' => AnonymousResourceCollection::class, 'reservations_count' => "mixed", 4 => "\Illuminate\Http\Resources\MergeValue|mixed"])]
    public function toArray($request): array
    {
        return [
            'user' => UserResource::make($this->whenLoaded('user')),
            'images' => ImageResource::collection($this->whenLoaded('images')),
            'tags' => TagResource::collection($this->whenLoaded('tags')),
//            'featured_image' => ImageResource::make($this->whenLoaded('featuredImage')),
            'reservations_count' => $this->resource->reservations_count ?? 0,

            $this->merge(Arr::except(parent::toArray($request), [
                'user_id', 'created_at', 'updated_at',
                'deleted_at'
            ]))
        ];
    }
}
