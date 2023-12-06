<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCityRequest;
use App\Models\City;


class CityController extends Controller
{
    public function index()
    {
        $sortBy = request('sort_by');
        $sortOrder = request('sort_order');

        $cities = City::withCount(['flightsTo', 'flightsFrom'])
            ->order($sortBy, $sortOrder)
            ->filter(request(['airline']))
            ->paginate(10);

        return view('cities', [
            'cities' => $cities
        ]);
    }

    public function store(StoreCityRequest $request)
    {
        $attributes = $request->validated();

        return City::create($attributes);
    }

    public function update(StoreCityRequest $request, City $city)
    {
        $attributes = $request->validated();

        $city->update($attributes);

        return $city;
    }

    public function destroy(City $city)
    {
        return $city->delete();
    }
}
