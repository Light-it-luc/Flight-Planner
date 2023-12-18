<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\GetCitiesRequest;
use App\Http\Requests\StoreCityRequest;
use App\Models\City;

class CityController extends Controller
{
    public function index(GetCitiesRequest $request)
    {
        $all = $request->boolean('all', false);

        if ($all) {
            return City::select('id', 'name', 'country')
                ->orderBy('name')
                ->get();
        }

        $sortBy = $request->input('sort_by', 'id');
        $ascending = $request->boolean('asc', true);

        $airlineId = $request->only(['airline']);

        return City::withCount(['flightsTo', 'flightsFrom'])
            ->order($sortBy, $ascending)
            ->filter($airlineId)
            ->paginate(10)
            ->withQueryString();
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
