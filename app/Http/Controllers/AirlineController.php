<?php

namespace App\Http\Controllers;

use App\Models\Airline;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Validator;


class AirlineController extends Controller
{
    public function index() {
        return view('airlines', [
            'airlines' => Airline::with('cities')
                ->withCount('flights')
                ->paginate(10)
        ]);
    }

    public function store(Request $request) {

        $validator = Validator::make($request->all(), [
            'name' => ['required', 'max:255', 'unique:airlines'],
            'description' => ['required']
        ]);

        if ($validator->fails()) {
            return response()
                ->json(['errors' => $validator->errors()], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $attributes = $validator->validated();

        return Airline::create($attributes);
    }
}
