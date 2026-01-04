<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class StoreSizeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() && $this->user()->role === 'Admin';
    }

    public function rules(): array
    {
        return [
            'id' => 'required|string|max:100|unique:sizes,id',
            'tipe' => 'required|string|max:100',
        ];
    }

    public function messages(): array
    {
        return [
            'id.unique' => 'ID Size ini sudah terdaftar, gunakan ID lain.',
            'id.required' => 'ID Size wajib diisi.',
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->json([
            'success' =>false,
            'message' => 'Validation errors',
            'errors' => [
                $validator->errors()
            ]
        ],400));
    }
}
