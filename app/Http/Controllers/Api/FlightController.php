<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\GetFlightsRequest;
use App\Models\Flight;

class FlightController extends Controller
{
    public function index(GetFlightsRequest $request)
    {
        $all = $request->boolean('all', false);

        if ($all) {
            return Flight::select('id', 'flight_number', 'origin_city_id', 'dest_city_id', 'airline_id')
                ->get();
        }

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
}
