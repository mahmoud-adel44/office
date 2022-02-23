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

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Pipeline\Pipeline;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Validation\ValidationException;
use Str;

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
            HandleReservation::handle($request , $office)->load('office')
        );
    }

    public function show(Reservation $reservation)
    {
        //
    }

    public function update(Request $request, Reservation $reservation)
    {
        //
    }

    public function destroy(Reservation $reservation)
    {
        //
    }
}
