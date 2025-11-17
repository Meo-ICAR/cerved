<?php

namespace App\Http\Requests;

use App\Models\Report;
use Illuminate\Foundation\Http\FormRequest;

class StoreReportRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true; // Update this with your authorization logic if needed
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'piva' => [
                'required',
                'string',
                'size:11',
                'regex:/^[0-9]+$/i',
                function ($attribute, $value, $fail) {
                    if (Report::where('piva', $value)->exists()) {
                        $fail('Un report con questa P.IVA esiste già.');
                    }
                },
            ],
        ];
    }

    public function messages()
    {
        return [
            'piva.required' => 'Il campo P.IVA è obbligatorio',
            'piva.size' => 'Il campo P.IVA deve essere di 11 cifre',
            'piva.regex' => 'Il campo P.IVA può contenere solo numeri',
        ];
    }
}
