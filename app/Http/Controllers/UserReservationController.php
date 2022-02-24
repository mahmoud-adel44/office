<?php

namespace App\Http\Controllers;

use App\Action\HandleReservation;
use App\Http\Pipelines\Filtration\Reservation\FilterByDate;
use App\Http\Pipelines\Filtration\Reservation\FilterByOfficeId;
use App\Http\Pipelines\Filtration\Reservation\FilterByStatus;
use App\Http\Pipelines\Filtration\Reservation\FilterByUserId;
use App\Http\Pipelines\Filtration\Reservation\ReturnWithFeaturedImage;
use App\Http\Requests\StoreUserReservationRequest;
use App\Http\Requests\UserReservationRequest;
use App\Http\Resources\ReservationResource;
use App\Models\Office;
use App\Models\Reservation;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Pipeline\Pipeline;

class UserReservationController extends Controller
{
    public function index(UserReservationRequest $request): AnonymousResourceCollection
    {
        $pipes = [
            FilterByUserId::class,
            FilterByOfficeId::class,
            FilterByStatus::class,
            FilterByDate::class,
            ReturnWithFeaturedImage::class,
        ];

        return ReservationResource::collection(
            app(Pipeline::class)
                ->send(Reservation::query())
                ->through($pipes)
                ->thenReturn()
                ->paginate(request('per_page', 20))
        );
    }

    public function store(StoreUserReservationRequest $request, Office $office): ReservationResource
    {
        return ReservationResource::make(
            HandleReservation::handle($request, $office)->load('office')
        );
    }

    public function cancel(Reservation $reservation): ReservationResource
    {
        $reservation->update([
            'status' => Reservation::STATUS_CANCELLED
        ]);

        return ReservationResource::make(
            $reservation->load('office')
        );
    }

}
