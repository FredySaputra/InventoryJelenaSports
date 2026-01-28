<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class UpdateStokRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() && $this->user()->role === 'Admin';
    }

    public function rules(): array
    {
        return [
            'idProduk' => 'required|exists:produks,id',
            'idSize' => 'required|exists:sizes,id',
            'jumlah'   => 'nullable|integer|min:0',   
            'min_stok' => 'nullable|integer|min:0',
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
