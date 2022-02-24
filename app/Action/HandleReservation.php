<?php

namespace App\Action;

use App\Models\Office;
use App\Models\Reservation;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

final class HandleReservation
{
    public static function handle($request, Office $office)
    {
        return Cache::lock('reservations_office_' . $office->id, 10)
            ->block(3, function () use ($request, $office) {

                $numberOfDays = Carbon::parse($request->end_date)->endOfDay()->diffInDays(
                        Carbon::parse($request->start_date)->startOfDay()
                    ) + 1;

                throw_if($office->reservations()->activeBetween($request->start_date, $request->end_date)->exists(),
                    ValidationException::withMessages([
                        'office_id' => 'You cannot make a reservation during this time'
                    ])
                );

                $price = $numberOfDays * $office->price_per_day;

                if ($numberOfDays >= 28 && $office->monthly_discount) {
                    $price -= ($price * $office->monthly_discount / 100);
                }


                return $office->reservations()->create(
                    array_merge(
                        $request->except('office_id'),
                        [
                            'user_id' => auth()->id(),
                            'wifi_password' => Str::random(),
                            'status' => Reservation::STATUS_ACTIVE,
                            'price' => $price,
                        ]
                    )
                );
            });
    }
}
