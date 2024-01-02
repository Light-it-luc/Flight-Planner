<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\GetFlightsRequest;
use App\Http\Requests\StoreFlightRequest;
use App\Models\Flight;

class FlightController extends Controller
{
    public function index(GetFlightsRequest $request)
    {
        $sortBy = $request->input('sort_by', 'id');
        $asc = $request->boolean('asc', true);

        $airlineId = $request->integer('airline_id', 0);
        $originId = $request->integer('origin_id', 0);
        $destinationId = $request->integer('destination_id', 0);

        $departureDate = $request->string('departure_date', '');
        $arrivalDate = $request->string('arrival_date', '');

        return Flight::with(['airline', 'origin', 'destination'])
            ->order($sortBy, $asc)
            ->filterByOrigin($originId)
            ->filterByDestination($destinationId)
            ->filterByAirline($airlineId)
            ->filterByDeparture($departureDate)
            ->filterByArrival($arrivalDate)
            ->paginate(10)
            ->withQueryString();
    }

    public function store(StoreFlightRequest $request)
    {
        $attribiutes = $request->validated();

        return Flight::create($attribiutes);
    }

    public function update(StoreFlightRequest $request, Flight $flight)
    {
        $attributes = $request->validated();

        return $flight->update($attributes);
    }

    public function destroy(Flight $flight)
    {
        return $flight->delete();
    }
}
