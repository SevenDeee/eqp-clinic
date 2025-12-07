<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StorePrescriptionRequest extends FormRequest
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
            'patient_id' => ['required', 'exists:patients,id'],

            'far' => ['nullable', 'array'],
            'far.od' => ['nullable', 'array'],
            'far.od.sphere' => ['nullable', 'numeric'],
            'far.od.cylinder' => ['nullable', 'numeric'],
            'far.od.axis' => ['nullable', 'integer', 'min:0', 'max:180'],
            'far.od.monopd' => ['nullable', 'numeric', 'min:0'],

            'far.os' => ['nullable', 'array'],
            'far.os.sphere' => ['nullable', 'numeric'],
            'far.os.cylinder' => ['nullable', 'numeric'],
            'far.os.axis' => ['nullable', 'integer', 'min:0', 'max:180'],
            'far.os.monopd' => ['nullable', 'numeric', 'min:0'],

            'near' => ['nullable', 'array'],
            'near.od' => ['nullable', 'array'],
            'near.od.sphere' => ['nullable', 'numeric'],
            'near.od.cylinder' => ['nullable', 'numeric'],
            'near.od.axis' => ['nullable', 'integer', 'min:0', 'max:180'],
            'near.od.monopd' => ['nullable', 'numeric', 'min:0'],

            'near.os' => ['nullable', 'array'],
            'near.os.sphere' => ['nullable', 'numeric'],
            'near.os.cylinder' => ['nullable', 'numeric'],
            'near.os.axis' => ['nullable', 'integer', 'min:0', 'max:180'],
            'near.os.monopd' => ['nullable', 'numeric', 'min:0'],

            'remarks' => ['nullable', 'string', 'max:255'],
            // 'prescribed_by' => ['required', 'numeric'],
        ];
    }
}
