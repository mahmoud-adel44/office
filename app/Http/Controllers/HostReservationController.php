<?php

namespace App\Http\Controllers;

use App\Http\Pipelines\Filtration\Reservation\FilterByDate;
use App\Http\Pipelines\Filtration\Reservation\FilterByOfficeId;
use App\Http\Pipelines\Filtration\Reservation\FilterByStatus;
use App\Http\Pipelines\Filtration\Reservation\FilterByOfficeForUser;
use App\Http\Pipelines\Filtration\Reservation\ReturnWithFeaturedImage;
use App\Http\Requests\HostReservationRequest;
use App\Http\Resources\ReservationResource;
use App\Models\Reservation;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Pipeline\Pipeline;

class HostReservationController extends Controller
{
    public function index(HostReservationRequest $request): AnonymousResourceCollection
    {
        $pipes = [
            FilterByOfficeForUser::class,
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

    public function store(Request $request)
    {
        //
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
