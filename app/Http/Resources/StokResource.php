<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class StokResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'idProduk' => $this->idProduk,
            'idSize' => $this->idSize,
            'stok' => $this->stok,
            'updated_at' => $this->updated_at,
        ];
    }
}
