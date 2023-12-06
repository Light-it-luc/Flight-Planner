<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreAirlineRequest;
use App\Http\Requests\UpdateAirlineRequest;
use App\Models\Airline;

class AirlineController extends Controller
{
    public function index()
    {
        $sortBy = request('sort_by');
        $sortOrder = request('sort_order');

        $airlines = Airline::with('cities')
            ->withCount('flights')
            ->order($sortBy, $sortOrder)
            ->filter(request(['city', 'flights']))
            ->paginate(10);

        return view('airlines', [
            'airlines' => $airlines
        ]);
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
