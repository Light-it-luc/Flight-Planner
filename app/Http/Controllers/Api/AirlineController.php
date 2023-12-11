<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreAirlineRequest;
use App\Http\Requests\UpdateAirlineRequest;
use App\Models\Airline;
use Illuminate\Http\Request;

class AirlineController extends Controller
{
    public function index()
    {
        return Airline::with('cities')
            ->withCount('flights')
            ->paginate(10)
            ->withQueryString();
    }

    public function store(StoreAirlineRequest $request) {

        $attributes = $request->validated();

        return Airline::create($attributes);
    }

    public function update(UpdateAirlineRequest $request, Airline $airline) {

        $attributes = $request->validated();

        return $airline->update($attributes);
    }

    public function destroy(Airline $airline) {

        return $airline->delete();
    }
}
