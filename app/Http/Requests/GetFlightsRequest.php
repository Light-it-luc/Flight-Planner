<?php

namespace App\Http\Requests;

use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;

class GetFlightsRequest extends FormRequest
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
            'sort_by' => [Rule::in(['arrival_at', 'departure_at'])],
            'origin_id' => ['sometimes', 'exists:cities,id'],
            'destination_id' => ['sometimes', 'exists:cities,id'],
            'airline_id' => ['sometimes', 'exists:airlines,id'],
            'departure_date' => ['sometimes', 'date'],
            'arrival_date' => ['sometimes', 'date']
        ];
    }
}
