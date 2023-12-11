<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class CityViewController extends Controller
{
    public function index()
    {
        return view('cities');
    }
}
