<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreCityRequest;
use App\Models\City;
use Illuminate\Http\Request;

class CityController extends Controller
{
    public function index()
    {
        return City::withCount(['flightsTo', 'flightsFrom'])
            ->paginate(10)
            ->withQueryString();
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

    public function destroy(City $city) {

        return $city->delete();
    }
}
