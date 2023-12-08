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
        $records_per_page = 10;

        $page = $request->input('page', 1);
        $page = ($page > 0)? $page: 1;

        $offset = ($page - 1) * $records_per_page;

        return Airline::with('cities')
                ->withCount('flights')
                ->skip($offset)
                ->take($records_per_page)
                ->get();
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
