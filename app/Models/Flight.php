<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Flight extends Model
{
    protected $guarded = ['id'];

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
        $uppercaseChars = collect()->range('A', 'Z');
        $sevenDigitNum = rand(1000000, 9999999);

        return $uppercaseChars->random() . strval($sevenDigitNum);
    }
}
