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
        $records_per_page = 10;

        $page = $request->input('page', 1);
        $page = ($page > 0)? $page: 1;

        $offset = ($page - 1) * $records_per_page;

        return City::withCount(['flightsTo', 'flightsFrom'])
            ->skip($offset)
            ->take($records_per_page)
            ->get();
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
