<?php

namespace Modules\Package\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'origin_city'           => ['required', 'string', 'max:100'],
            'origin_address'        => ['required', 'string', 'max:255'],
            'destination_city'      => ['required', 'string', 'max:100'],
            'destination_address'   => ['required', 'string', 'max:255'],
            'weight_grams'          => ['required', 'integer', 'min:0'],
        ];
    }

    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }
}
