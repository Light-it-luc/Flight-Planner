<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class City extends Model
{
    protected $guarded = ['id'];

    public function flightsFrom(): HasMany
    {
        return $this->hasMany(Flight::class, 'origin_city_id');
    }

    public function flightsTo(): HasMany
    {
        return $this->hasMany(Flight::class, 'destination_city_id');
    }

    public function airlines(): BelongsToMany
    {
        return $this->belongsToMany(Airline::class);
    }

    public function scopeOrder(Builder $query, string $sortBy, bool $ascending): void
    {
        $order = $ascending ? 'asc' : 'desc' ;

        $query->orderBy($sortBy, $order);
    }

    public function scopeFilter(Builder $query, array $filters): void
    {
        $query->when($filters['airline'] ?? false, function($query, $airlineId) {
            $query->whereHas('airlines', function($query) use($airlineId) {
                $query->where('airline_id', $airlineId);
            });
        });
    }
}
