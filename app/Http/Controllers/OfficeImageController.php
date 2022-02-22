<?php

namespace App\Http\Controllers;

use App\Http\Requests\OfficeImageRequest;
use App\Http\Resources\ImageResource;
use App\Models\Image;
use App\Models\Office;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;
use Throwable;

class OfficeImageController extends Controller
{
    /** @noinspection NullPointerExceptionInspection */
    public function store(OfficeImageRequest $request, Office $office): jsonResource
    {
        $image = $office->images()->create([
            'path' => $request->file('image')->storePublicly('/' , ['disk' => 'public'])
        ]);
        return ImageResource::make($image);
    }

    /**
     * @throws AuthorizationException
     * @throws Throwable
     */
    public function delete(Office $office, Image $image): void
    {
        $this->authorize('update', $office);

        throw_if($office->images()->count() === 1,
            ValidationException::withMessages(['image' => 'Cannot delete the only image.'])
        );

        throw_if($office->featured_image_id === $image->id,
            ValidationException::withMessages(['image' => 'Cannot delete the featured image.'])
        );

        Storage::delete($image->path);

        $image->delete();
    }


}
