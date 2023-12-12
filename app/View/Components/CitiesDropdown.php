<?php

namespace App\View\Components;

use App\Models\City;
use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class CitiesDropdown extends Component
{
    /**
     * Create a new component instance.
     */
    public function __construct()
    {
        //
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.cities-dropdown', [
            'cities' => City::select('id', 'name')
                ->orderBy('name', 'asc')
                ->get()
        ]);
    }
}
