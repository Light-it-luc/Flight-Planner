<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Flight extends Model
{
    protected $guarded = ['id', 'flight_number'];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($flight) {
            $flight->flight_number = static::generateFlightNumber();
        });
    }

    public function origin(): BelongsTo
    {
        return $this->belongsTo(City::class, 'origin_city_id');
    }

    public function destination(): BelongsTo
    {
        return $this->belongsTo(City::class, 'dest_city_id');
    }

    public function airline(): BelongsTo
    {
        return $this->belongsTo(Airline::class);
    }

    public static function generateFlightNumber($length = 8): string
    {
        $uppercaseChars = collect(range('A', 'Z'));
        $sevenDigitNum = rand(1000000, 9999999);

        return $uppercaseChars->random() . $sevenDigitNum;
    }

    public function scopeOrder(Builder $query, string $sortBy, bool $ascending): void
    {
        $order = $ascending ? 'asc' : 'desc';

        $query->orderBy($sortBy, $order);
    }

    public function scopeFilterByOrigin(Builder $query, int $originCityId): void
    {
        $query->when($originCityId, function($query) use ($originCityId) {
            $query->where('origin_city_id', $originCityId);
        });
    }

    public function scopeFilterByDestination(Builder $query, int $destCityId): void
    {
        $query->when($destCityId, function($query) use ($destCityId) {
            $query->where('dest_city_id', $destCityId);
        });
    }

    public function scopeFilterByAirline(Builder $query, int $airlineId): void
    {
        $query->when($airlineId, function($query) use ($airlineId) {
            $query->where('airline_id', $airlineId);
        });
    }

    public function scopeFilterByDeparture(Builder $query, string $departure): void
    {
        $query->when($departure, function ($query) use ($departure) {
            $query->whereDate('departure_at', '=', Carbon::parse($departure));
        });
    }

    public function scopeFilterByArrival(Builder $query, string $arrival): void
    {
        $query->when($arrival, function ($query) use ($arrival) {
            $query->whereDate('arrival_at', '=', Carbon::parse($arrival));
        });
    }
}
