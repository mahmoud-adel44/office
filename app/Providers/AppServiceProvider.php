<?php

namespace App\Providers;

use App\Models\Office;
use App\Models\Reservation;
use App\Models\User;
use App\Observers\ReservationObserver;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        //to stop math assignment (to don't make fillable )
        Model::unguard();

        Relation::enforceMorphMap([
            'office' => Office::class ,
            'user' => User::class ,
        ]);

        Builder::macro('betweenDates', function ($from, $to) {
            return $this->where(function (Builder $builder) use ($to, $from) {
                $builder
                    ->whereBetween('start_date', [$from, $to])
                    ->orWhereBetween('end_date', [$from, $to])
                    ->orWhere(function (Builder $builder) use ($to, $from) {
                        $builder
                            ->where('start_date', '<', $from)
                            ->where('end_date', '>', $to);
                    });
            });
        });

        Reservation::observe(ReservationObserver::class);
    }
}
