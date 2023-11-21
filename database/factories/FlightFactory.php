<?php

namespace Database\Factories;

use App\Models\City;
use App\Models\Airline;
use App\Models\Flight;
use Illuminate\Support\Carbon;
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
        $cities = City::factory(2)->create();
        $airline = Airline::factory()->create();

        foreach($cities as $city) {
            $city->airlines()->attach($airline);
        }

        $departure = Carbon::now()->addDays(rand(0, 365));

        return [
            'flight_number' => Flight::generateFlightNumber(),
            'airline_id' => $airline->id,
            'origin_city_id' => $cities[0]->id,
            'dest_city_id' => $cities[1]->id,
            'departure' => $departure,
            'arrival' => $departure->copy()->addHours(rand(1, 16))
        ];
    }
}
