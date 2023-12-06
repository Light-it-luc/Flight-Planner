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
        $query->when($filters['city'] ?? false, function($query, $city) {
            $query->whereHas('cities', function($query) use($city) {
                $query->where('city_id', $city);
            });
        });

        $query->when(isset($filters['flights']), function ($query) use ($filters) {
            $query->having('flights_count', '=', $filters['flights']);
        });
    }
}
