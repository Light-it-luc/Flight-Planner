<?php

namespace Tests\Feature\FlightController;

use Database\Factories\FlightFactory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Response;
use Tests\TestCase;

class DeleteTest extends TestCase
{
    use RefreshDatabase;

    const BASE_ENDPOINT = 'api/v1/flights/';

    protected $flight;

    protected function setUp(): void
    {
        parent::setUp();

        $this->flight = FlightFactory::new()->create();
    }

    public function test_correctly_deletes_result(): void
    {
        $response = $this->delete(self::BASE_ENDPOINT . $this->flight->id);

        $response->assertSuccessful();

        $this->assertDatabaseCount('flights', 0);
    }

    public function test_returns_404_status_if_flight_not_found(): void
    {
        $response = $this->delete(self::BASE_ENDPOINT . '0');

        $response->assertStatus(Response::HTTP_NOT_FOUND);

        $this->assertDatabaseCount('flights', 1);
    }
}
