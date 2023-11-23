<?php

namespace Database\Factories;

use App\Models\City;
use App\Models\Airline;
use App\Models\Flight;
use Carbon\CarbonImmutable;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Flight>
 */
class FlightFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $departure = CarbonImmutable::now()->addDays(rand(0, 7));

        return [
            'flight_number' => Flight::generateFlightNumber(),
            'airline_id' => (Airline::factory()->create())->id,
            'origin_city_id' => (City::factory()->create())->id,
            'dest_city_id' => (City::factory()->create())->id,
            'departure' => $departure,
            'arrival' => $departure->addHours(rand(1, 18))
        ];
    }
}
