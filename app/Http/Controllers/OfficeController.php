<?php

namespace App\Http\Controllers;

use App\Http\Resources\OfficeResource;
use App\Models\Office;
use App\Models\Reservation;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class OfficeController extends Controller
{
    public function index(): AnonymousResourceCollection
    {
        $offices = Office::query()
            ->where('approval_status', Office::APPROVAL_APPROVED)
            ->where('hidden', FALSE)
            ->when(
                request('host_id'),
                fn($builder) => $builder->whereUserId(request('host_id'))
            )
            ->when(
                request('user_id'),
                fn(Builder $builder) => $builder->whereRelation(
                    'reservations',
                    'user_id',
                    '=',
                    request('user_id')
                )
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
}


