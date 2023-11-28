<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCityRequest;
use App\Models\City;


class CityController extends Controller
{
    public function index() {
        return view('cities', [
            'cities' => City::withCount(['flightsTo', 'flightsFrom'])
                            ->paginate(10)
        ]);
    }

    public function store(StoreCityRequest $request) {

        $attributes = $request->validated();

        return City::create($attributes);
    }

    public function update(StoreCityRequest $request, City $city) {

        $attributes = $request->validated();

        $city->update($attributes);

        return $city;
    }
}
