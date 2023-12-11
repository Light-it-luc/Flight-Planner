<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class AirlineViewController extends Controller
{
    public function index()
    {
        return view('airlines');
    }
}
