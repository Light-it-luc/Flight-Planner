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
        return $this->hasMany(Flight::class, 'dest_city_id');
    }

    public function airlines(): BelongsToMany
    {
        return $this->belongsToMany(Airline::class);
    }

    public function scopeOrder(Builder $query, ?string $sortBy, ?string $sortOrder): void
    {
        $allowedColumns = ['id', 'name'];
        $allowedOrders = ['asc', 'desc'];

        $sortOrder = strtolower($sortOrder);

        $sortBy = in_array($sortBy, $allowedColumns)? $sortBy: 'id';
        $sortOrder = in_array($sortOrder, $allowedOrders)? $sortOrder: 'asc';

        $query->orderBy($sortBy, $sortOrder);
    }

    public function scopeFilter(Builder $query, array $filters): void
    {
        $query->when($filters['airline'] ?? false, function($query, $airline) {
            $query->whereHas('airlines', function($query) use($airline) {
                $query->where('airline_id', $airline);
            });
        });
    }
}
