<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreOfficeRequest;
use App\Http\Resources\OfficeResource;
use App\Models\Office;
use App\Models\Reservation;
use App\Models\User;
use App\Notifications\OfficePendingApprovalNotification;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;
use Throwable;

class OfficeController extends Controller
{
    public function index(): AnonymousResourceCollection
    {
        $offices = Office::query()
            ->when(request('user_id') && auth()->user() && request('user_id') == auth()->id(),
                fn($builder) => $builder,
                fn($builder) => $builder->where('approval_status', Office::APPROVAL_APPROVED)->where('hidden', false)
            )
            ->when(request('user_id'), fn($builder) => $builder->whereUserId(request('user_id')))
            ->when(request('visitor_id'),
                fn($builder) => $builder->whereRelation('reservations', 'user_id', '=', request('visitor_id'))
            )
            ->when(
                request('lat') && request('lng'),
                fn($builder) => $builder->nearestTo(request('lat'), request('lng')),
                fn(Builder $builder) => $builder->orderBy('id', 'ASC')
            )
            ->with(['images', 'tags', 'user'])
            ->withCount([
                'reservations' => fn(Builder $builder) => $builder->where('status', Reservation::STATUS_ACTIVE)
            ])
            ->paginate(20);
        return OfficeResource::collection(
            $offices
        );
    }

    public function show(Office $office): OfficeResource
    {
        $office->loadCount([
            'reservations' => fn(Builder $builder) => $builder->where('status', Reservation::STATUS_ACTIVE)
        ]);
        $office->load(['images', 'tags', 'user']);
        return OfficeResource::make($office);

    }

    public function create(StoreOfficeRequest $request) : OfficeResource
    {
        $office = DB::transaction( function () use ($request) {
            $office = Office::create($request->except('tags') + ['user_id' => auth('sanctum')->id()]);
            if (isset($request->tags)) {
                $office->tags()->attach($request->only('tags')['tags']);
            }
            return $office;
        });

        return OfficeResource::make($office->load(['tags', 'user', 'images']));
    }

    public function update(StoreOfficeRequest $request, Office $office): OfficeResource
    {
        $office->fill($request->except('tags'));

        if ($requiresReview = $office->isDirty(['lat', 'lng', 'price_per_day'])) {
            $office->fill(['approval_status' => Office::APPROVAL_PENDING]);
        }

        DB::transaction(static function () use ($office, $request) {
            $office->save();
            if (isset($request->tags)) {
                $office->tags()->sync($request->only('tags')['tags']);
            }
        });
//        if ($requiresReview) {
//            Notification::send(User::where('is_admin', true)->get(), new OfficePendingApprovalNotification($office));
//        }

        return OfficeResource::make($office->load(['tags', 'user', 'images']));
    }

    /**
     * @throws AuthorizationException
     * @throws Throwable
     */
    public function delete(Office $office): void
    {
        $this->authorize('delete', $office);
        throw_if(
            $office->reservations()->where('status', Reservation::STATUS_ACTIVE)->exists(),
            ValidationException::withMessages(['office' => 'Cannot delete this office!'])
        );

        $office->images()->each(function ($image) {
            Storage::delete($image->path);

            $image->delete();
        });

        $office->delete();
    }




}


