<?php

namespace Tests\Feature\FlightController;

use App\Models\Flight;
use Carbon\Carbon;
use Database\Factories\AirlineFactory;
use Database\Factories\CityFactory;
use Database\Factories\FlightFactory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\Response;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class UpdateTest extends TestCase
{
    use RefreshDatabase;

    const BASE_ENDPOINT = 'api/v1/flights/';

    protected $flight;
    protected $requestBody;

    protected function setUp(): void
    {
        parent::setUp();

        $origin = CityFactory::new()->create([
            'name' => 'Montevideo',
            'country' => 'Uruguay'
        ]);

        $destination = CityFactory::new()->create([
            'name' => 'Buenos Aires',
            'country' => 'Argentina'
        ]);

        $airline = AirlineFactory::new()->create([
            'name' => 'Aerolíneas Argentinas'
        ]);

        $airline->cities()->syncWithoutDetaching([$origin->id, $destination->id]);

        $this->requestBody = [
            'origin_city_id' => $origin->id,
            'destination_city_id' => $destination->id,
            'airline_id' => $airline->id,
            'departure_at' => '2023-10-15 09:15',
            'arrival_at' => '2023-10-15 12:20'
        ];

        $this->flight = FlightFactory::new()->create($this->requestBody);
    }

    public function test_status_is_200_on_correct_update(): void
    {
        $body = $this->requestBody;

        $body['arrival_at'] = '2023-10-15 23:30';

        $response = $this->patch(self::BASE_ENDPOINT . $this->flight->id, $body);

        $response->assertSuccessful();
    }

    public function test_record_is_correctly_updated(): void
    {
        $body = $this->requestBody;

        $newArrival = '2023-10-15 13:30';

        $body['arrival_at'] = $newArrival;

        $this->patch(self::BASE_ENDPOINT . $this->flight->id, $body);

        $this->assertDatabaseCount('flights', 1);

        $updatedFlight = Flight::first();

        $this->assertEquals($updatedFlight->arrival_at, Carbon::parse($newArrival));
    }

    public function test_other_properties_are_not_modified(): void
    {
        $body = $this->requestBody;

        $newArrival = '2023-10-15 15:30';

        $body['arrival_at'] = $newArrival;

        $this->patch(self::BASE_ENDPOINT . $this->flight->id, $body);

        $updatedFlight = Flight::first()->toArray();

        unset($updatedFlight['arrival_at']);

        $this->assertDatabaseHas('flights', $updatedFlight);
    }

    public function test_update_fails_if_a_validation_rule_fails(): void
    {
        $body = $this->requestBody;

        $body['origin_city_id'] = 0;

        $response = $this->patch(self::BASE_ENDPOINT . $this->flight->id, $body);

        $response->assertStatus(Response::HTTP_FOUND);
    }
}
