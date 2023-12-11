<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreAirlineRequest;
use App\Http\Requests\UpdateAirlineRequest;
use App\Models\Airline;
use Illuminate\Http\Request;

class AirlineController extends Controller
{
    public function indexView()
    {
        return view('airlines');
    }

    public function index(Request $request)
    {
        $sortBy = $request->input('sort_by', 'id');
        $ascending = $request->boolean('asc', true);

        return Airline::with('cities')
            ->withCount('flights')
            ->order($sortBy, $ascending)
            ->filterByCity($request->input('city'))
            ->filterByFlights($request->input('flights'))
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
