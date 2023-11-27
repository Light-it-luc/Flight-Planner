<?php

namespace App\Http\Controllers;
use App\Models\City;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class CityController extends Controller
{
    public function index() {
        return view('cities', [
            'cities' => City::withCount(['flightsTo', 'flightsFrom'])->paginate(10)
        ]);
    }

    public function store(Request $request) {
        $rules = [
            'name' => ['required', 'max:255'],
            'country' => [
                'required', 'max:255',
                Rule::unique('cities', 'country')
                    ->where(function ($query) use ($request) {
                        return $query->where('name', $request->name);
                    })
                ]
            ];

        $attributes = $request->validate($rules);

        return City::create($attributes);
    }
}
