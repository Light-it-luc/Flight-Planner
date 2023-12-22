<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\GetFlightsRequest;
use App\Http\Requests\StoreFlightRequest;
use App\Models\Flight;
use Illuminate\Http\Request;

class FlightController extends Controller
{
    public function index(GetFlightsRequest $request)
    {
        $sortBy = $request->input('sort_by', 'id');
        $asc = $request->boolean('asc', true);

        $airlineId = $request->integer('airline', 0);
        $originId = $request->integer('origin', 0);
        $destinationId = $request->integer('destination', 0);

        $departureDate = $request->string('departure', '');
        $arrivalDate = $request->string('arrival', '');

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

    public function destroy(Flight $flight)
    {
        return $flight->delete();
    }
}
