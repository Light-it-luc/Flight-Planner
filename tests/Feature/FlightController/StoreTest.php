<?php

namespace Tests\Feature\FlightController;

use Database\Factories\AirlineFactory;
use Database\Factories\CityFactory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Response;
use Illuminate\Testing\Fluent\AssertableJson;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class StoreTest extends TestCase
{
    use RefreshDatabase;

    const ENDPOINT = 'api/v1/flights';

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
    }

    public function test_status_code_is_created_on_correct_inputs(): void
    {
        $response = $this->postJson(self::ENDPOINT, $this->requestBody);

        $response->assertStatus(Response::HTTP_CREATED);
    }

    public function test_new_record_is_persisted_to_the_db(): void
    {
        $this->postJson(self::ENDPOINT, $this->requestBody);

        $this->assertDatabaseHas('flights', $this->requestBody);
    }

    public function test_new_record_is_returned(): void
    {
        $response = $this->postJson(self::ENDPOINT, $this->requestBody);

        $response->assertJsonFragment($this->requestBody);
        $response->assertJson(fn(AssertableJson $json) =>
            $json->hasAll(['id', 'flight_number', 'created_at', 'updated_at'])
                 ->etc()
        );
    }

    public function test_creation_fails_on_inexistent_origin_id(): void
    {
        $body = $this->requestBody;

        $body['origin_city_id'] = 0;

        $response = $this->postJson(self::ENDPOINT, $body);

        $response->assertUnprocessable();
    }

    public function test_creation_fails_on_inexistent_destination_id(): void
    {
        $body = $this->requestBody;

        $body['destination_city_id'] = 0;

        $response = $this->postJson(self::ENDPOINT, $body);

        $response->assertUnprocessable();
    }

    public function test_creation_fails_on_inexistent_airline_id(): void
    {
        $body = $this->requestBody;

        $body['airline_id'] = 0;

        $response = $this->postJson(self::ENDPOINT, $body);

        $response->assertUnprocessable();
    }

    public function test_creation_fails_if_same_origin_and_destination(): void
    {
        $body = $this->requestBody;

        $body['origin_city_id'] = $this->requestBody['destination_city_id'];

        $response = $this->postJson(self::ENDPOINT, $body);

        $response->assertUnprocessable();
    }

    public function test_creation_fails_id_arrival_before_departure(): void
    {
        $body = $this->requestBody;

        $body['arrival_at'] = '2023-10-15 07:20';

        $response = $this->postJson(self::ENDPOINT, $body);

        $response->assertUnprocessable();
    }

    public function test_creation_fails_if_airline_does_not_work_with_origin(): void
    {
        $body = $this->requestBody;

        $newCity = CityFactory::new()->create();

        $body['origin_city_id'] = $newCity->id;

        $response = $this->postJson(self::ENDPOINT, $body);

        $response->assertUnprocessable();
    }

    public function test_creation_fails_if_airline_does_not_work_with_destination(): void
    {
        $body = $this->requestBody;

        $newCity = CityFactory::new()->create();

        $body['destination_city_id'] = $newCity->id;

        $response = $this->postJson(self::ENDPOINT, $body);

        $response->assertUnprocessable();
    }

    public static function requiredPropertyProvider(): array
    {
        return [
            'missing_origin_city_id' => ['origin_city_id'],
            'missing_destination_city_id' => ['destination_city_id'],
            'missing_airline_id' => ['airline_id'],
            'missing_departure_datetime' => ['departure_at'],
            'missing_arrival_datetime' => ['arrival_at']
        ];
    }

    #[DataProvider('requiredPropertyProvider')]
    public function test_creation_fails_if_a_required_attribute_is_missing($property): void
    {
        $body = $this->requestBody;

        unset($body[$property]);

        $response = $this->patch(self::ENDPOINT, $body);

        $response->assertStatus(Response::HTTP_METHOD_NOT_ALLOWED);
    }
}
