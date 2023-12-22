<?php

namespace App\Models;

use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Airline extends Model
{
    protected $guarded = ['id'];

    public function flights(): HasMany
    {
        return $this->hasMany(Flight::class);
    }

    public function cities(): BelongsToMany
    {
        return $this->belongsToMany(City::class);
    }

    public function scopeOrder(Builder $query, string $sortBy, bool $ascending): void
    {
        $order = $ascending ? 'asc' : 'desc';

        $query->orderBy($sortBy, $order);
    }

    public function scopeFilterByCity(Builder $query, ?int $cityId): void
    {
        $query->when($cityId, function($query, $cityId) {
            $query->whereHas('cities', function($query) use($cityId) {
                $query->where('city_id', $cityId);
            });
        });
    }

    public function scopeFilterByFlights(Builder $query, ?int $flights): void
    {
        $hasFlightsCount = Str::contains($query->getQuery()->toSql(), 'flights_count');

        $query->when(isset($flights), function ($query) use ($flights, $hasFlightsCount) {
            $query->when(! $hasFlightsCount, fn($query) => $query->withCount('flights'));
            $query->having('flights_count', '=', $flights);
        });
    }
}
