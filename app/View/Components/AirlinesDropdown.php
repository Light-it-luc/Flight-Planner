<?php

namespace App\View\Components;

use App\Models\Airline;
use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class AirlinesDropdown extends Component
{
    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.airlines-dropdown', [
            'airlines' => Airline::select('id', 'name')
                ->orderBy('name', 'asc')
                ->get()
        ]);
    }
}
