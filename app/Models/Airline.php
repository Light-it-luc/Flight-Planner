<?php

namespace App\Models;

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

    public function scopeOrder(Builder $query, ?string $sortBy, bool $ascending): void
    {
        $allowedColumns = ['id', 'name'];
        $order = $ascending ? 'asc' : 'desc';

        $sortBy = in_array($sortBy, $allowedColumns)? $sortBy: 'id';

        $query->orderBy($sortBy, $order);
    }

    public function scopeFilterByCity(Builder $query, ?string $city): void
    {
        $query->when($city, function($query, $city) {
            $query->whereHas('cities', function($query) use($city) {
                $query->where('city_id', $city);
            });
        });
    }

    public function scopeFilterByFlights(Builder $query, ?int $flights): void
    {
        $query->when(isset($flights), function ($query) use ($flights) {
            $query->having('flights_count', '=', $flights);
        });
    }
}
