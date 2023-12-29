<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreFlightRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'origin_city_id' => ['required', 'exists:cities,id'],
            'destination_city_id' => ['required', 'exists:cities,id', 'different:origin_city_id'],
            'airline_id' => [
                'required',
                'exists:airlines,id',
                Rule::exists('airline_city')->where('city_id', $this->input('origin_city_id')),
                Rule::exists('airline_city')->where('city_id', $this->input('destination_city_id')),
            ],
            'departure_at' => ['required', 'date_format:Y-m-d H:i'],
            'arrival_at' => ['required', 'date_format:Y-m-d H:i', 'after:departure_at']
        ];
    }
}
