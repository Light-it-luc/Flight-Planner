<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreAirlineRequest;
use App\Http\Requests\UpdateAirlineRequest;
use App\Models\Airline;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class AirlineController extends Controller
{
    public function index() {
        return view('airlines', [
            'airlines' => Airline::with('cities')
                ->withCount('flights')
                ->paginate(10)
        ]);
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
