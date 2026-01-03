<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProdukResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'nama' => $this->nama,
            'nama_lengkap' => $this->nama,
            'warna' => $this->warna,
            'idKategori' => $this->idKategori,
            'kategori_nama' => $this->kategori ? $this->kategori->nama : null,
            'stoks' => $this->whenLoaded('stoks'),
        ];
    }
}
