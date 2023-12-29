<?php

namespace Tests\Feature\FlightController;

use Database\Factories\AirlineFactory;
use Database\Factories\CityFactory;
use Database\Factories\FlightFactory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Response;
use Illuminate\Testing\Fluent\AssertableJson;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class IndexTest extends TestCase
{
    use RefreshDatabase;

    const ENDPOINT = 'api/v1/flights';

    protected $flight;

    protected function setUp(): void
    {
        parent::setUp();

        $mvd = CityFactory::new()->create([
            'name' => 'Montevideo',
            'country' => 'Uruguay'
        ]);

        $bsas = CityFactory::new()->create([
            'name' => 'Buenos Aires',
            'country' => 'Argentina'
        ]);

        $airline = AirlineFactory::new()->create([
            'name' => 'Aerolíneas Argentinas'
        ]);

        $airline->cities()->syncWithoutDetaching([$mvd->id, $bsas->id]);

        $this->flight = FlightFactory::new()->create([
            'origin_city_id' => $mvd->id,
            'destination_city_id' => $bsas->id,
            'airline_id' => $airline
        ]);
    }

    public function test_status_code_is_200(): void
    {
        $response = $this->get(self::ENDPOINT);

        $response->assertStatus(Response::HTTP_OK);
    }

    public function test_response_data_has_array_with_one_flight(): void
    {
        $response = $this->get(self::ENDPOINT);

        $response->assertJson(fn (AssertableJson $json) =>
            $json->has('data', 1)
                  ->whereType('data', 'array')
                  ->etc()
        );
    }

    public function test_retrieved_flights_have_all_regular_flight_attributes(): void
    {
        $response = $this->get(self::ENDPOINT);

        $response->assertJson(fn (AssertableJson $json) =>
            $json->has('data.0', fn(AssertableJson $flight) =>
                $flight->whereAllType([
                    'id' => 'integer',
                    'flight_number' => 'string',
                    'origin_city_id' => 'integer',
                    'destination_city_id' => 'integer',
                    'departure_at' => 'string',
                    'arrival_at' => 'string',
                    'created_at' => 'string',
                    'updated_at' => 'string'
                ])
                       ->etc()
            )
                ->etc()
        );
    }

    public function test_flights_have_origin_city_relationship(): void
    {
        $response = $this->get(self::ENDPOINT);

        $response->assertJson(fn (AssertableJson $json) =>
            $json->has('data.0', fn(AssertableJson $flight) =>
                $flight->where('origin', $this->flight->origin)
                       ->etc()
            )
                 ->etc()
        );
    }

    public function test_flights_have_destination_city_relationship(): void
    {
        $response = $this->get(self::ENDPOINT);

        $response->assertJson(fn (AssertableJson $json) =>
            $json->has('data.0', fn(AssertableJson $flight) =>
                $flight->where('destination', $this->flight->destination)
                       ->etc()
            )
                 ->etc()
        );
    }

    public function test_flights_have_airline_relationship(): void
    {
        $response = $this->get(self::ENDPOINT);

        $response->assertJson(fn (AssertableJson $json) =>
            $json->has('data.0', fn(AssertableJson $flight) =>
                $flight->where('airline', $this->flight->airline)
                       ->etc()
            )
                 ->etc()
        );
    }

    public function test_response_returns_10_flights_even_if_more_on_the_db(): void
    {
        $this->seed();

        $response = $this->get(self::ENDPOINT);

        $response->assertJson(fn (AssertableJson $json) =>
            $json->has('data', 10)
                 ->etc()
        );

        $this->assertDatabaseCount('flights', 51);
    }

    public function test_response_has_pagination_links(): void
    {
        $this->seed();

        $response = $this->get(self::ENDPOINT);

        $response->assertJson(fn (AssertableJson $json) =>
            $json->has('links')
                 ->has('links.0', fn(AssertableJson $paginationLink) =>
                    $paginationLink->has('url')
                                   ->has('label')
                                   ->has('active')
                 )
                 ->etc()
        );
    }

    public function test_pagination_links_mantain_query_string(): void
    {
        $this->seed();

        $endpoint = self::ENDPOINT . '?hello=world';

        $response = $this->get($endpoint);

        $response->assertJsonPath(
            'links.2.url',
            'http://localhost/' . $endpoint . '&page=2'
        );
    }
}
