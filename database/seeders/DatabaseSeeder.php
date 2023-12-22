<?php

namespace Database\Seeders;

use Database\Factories\AirlineFactory;
use Database\Factories\CityFactory;
use Database\Factories\FlightFactory;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $new_flights = 50;

        DB::transaction(function () use ($new_flights) {
            $cities = CityFactory::new()->count(25)->create();
            $airlines = AirlineFactory::new()->count(15)->create();

            for ($i = 0; $i < $new_flights; $i++) {
                [$origin, $dest] = $cities->random(2);
                $airline = $airlines->random();

                FlightFactory::new()
                    ->count(1)
                    ->create([
                        'origin_city_id' => $origin,
                        'dest_city_id' => $dest,
                        'airline_id' => $airline
                    ]);

                $airline->cities()->syncWithoutDetaching([$origin->id, $dest->id]);
            }
        });
    }
}
