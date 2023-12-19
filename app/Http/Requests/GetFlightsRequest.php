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
            'origin' => ['sometimes', 'exists:cities,id'],
            'destination' => ['sometimes', 'exists:cities,id'],
            'airline' => ['sometimes', 'exists:airlines,id'],
            'departure' => ['sometimes', 'date'],
            'arrival' => ['sometimes', 'date']
        ];
    }
}
