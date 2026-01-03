<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class StoreProdukRequest extends FormRequest
{
    /**
     * Pastikan hanya Admin yang boleh menambah produk
     */
    public function authorize(): bool
    {
        return $this->user() && $this->user()->role === 'Admin';
    }

    /**
     * Aturan validasi
     */
    public function rules(): array
    {
        return [
            'id' => 'required|string|max:100|unique:produks,id',
            'nama' => 'required|string|max:100',
            'warna' => 'nullable|string|max:50',
            'idKategori' => 'required|exists:kategoris,id',
            'idBahan' => 'required|exists:bahans,id',
        ];
    }

    public function messages(): array
    {
        return [
            'id.unique' => 'Kode Produk (ID) ini sudah terdaftar.',
            'idKategori.exists' => 'Kategori yang dipilih tidak valid.',
            'idBahan.exists' => 'Bahan yang dipilih tidak valid.'
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
