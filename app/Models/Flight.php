<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Flight extends Model
{
    use HasFactory;

    protected $guarded = [];

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
        $num = implode('', range('0', '9'));
        $alphas = implode('', range('A', 'Z'));

        $flight_n = $alphas[rand(0, strlen($alphas) - 1)];

        for($i = 1; $i < $length; $i++) {
            $flight_n .= $num[rand(0, strlen($num) - 1)];
        }

        return $flight_n;
    }
}
