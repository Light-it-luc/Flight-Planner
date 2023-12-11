<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCityRequest;
use App\Models\City;
use Illuminate\Http\Request;

class CityController extends Controller
{
    public function indexView()
    {
        return view('cities');
    }

    public function index(Request $request)
    {
        $sortBy = $request->input('sort_by', 'id');
        $ascending = $request->boolean('asc', true);

        return City::withCount(['flightsTo', 'flightsFrom'])
            ->order($sortBy, $ascending)
            ->filter($request->only(['airline']))
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
