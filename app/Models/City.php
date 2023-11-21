<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class City extends Model
{
    use HasFactory;

    protected $guarded = [];

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
}
