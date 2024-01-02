<?php

namespace Database\Factories;

use App\Models\Flight;
use Carbon\CarbonImmutable;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Flight>
 */
class FlightFactory extends Factory
{
    protected $model = Flight::class;

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
            'airline_id' => AirlineFactory::new(),
            'origin_city_id' => CityFactory::new(),
            'destination_city_id' => CityFactory::new(),
            'departure_at' => $departure,
            'arrival_at' => $departure->addHours(rand(1, 18))
        ];
    }
}
