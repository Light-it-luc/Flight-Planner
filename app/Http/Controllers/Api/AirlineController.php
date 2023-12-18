<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\GetAirlinesRequest;
use App\Http\Requests\StoreAirlineRequest;
use App\Http\Requests\UpdateAirlineRequest;
use App\Models\Airline;

class AirlineController extends Controller
{
    public function index(GetAirlinesRequest $request)
    {
        $all = $request->boolean('all', false);

        if ($all) {
            return Airline::select('id', 'name')
                ->orderBy('name')
                ->get();
        }

        $sortBy = $request->input('sort_by', 'id');
        $ascending = $request->boolean('asc', true);

        $cityId = $request->input('city');
        $numberOfFlights = $request->input('flights');

        return Airline::with('cities')
            ->withCount('flights')
            ->order($sortBy, $ascending)
            ->filterByCity($cityId)
            ->filterByFlights($numberOfFlights)
            ->paginate(10)
            ->withQueryString();
    }

    public function store(StoreAirlineRequest $request)
    {
        $attributes = $request->validated();

        return Airline::create($attributes);
    }

    public function update(UpdateAirlineRequest $request, Airline $airline)
    {
        $attributes = $request->validated();

        return $airline->update($attributes);
    }

    public function destroy(Airline $airline)
    {
        return $airline->delete();
    }
}
