<?php

namespace App\Http\Requests;

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
            'piva' => 'required|string|max:20',
            'israces' => 'boolean',
            'annotation' => 'nullable|string',
            'apicervedcode' => 'nullable|integer',
            'apicervedresponse' => 'nullable|array',
            'apiactivation' => 'nullable|date',
            'mediaresponse' => 'nullable|array',
            'user_id' => 'required|exists:users,id'
        ];
    }
}
