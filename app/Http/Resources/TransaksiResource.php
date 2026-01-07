<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TransaksiResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'tanggal' => $this->tanggalTransaksi,
            'total_item' => $this->totalItem,
            'total_harga' => $this->totalTransaksi,
            'pelanggan' => $this->whenLoaded('pelanggan', function() {
                return [
                    'id' => $this->pelanggan->id,
                    'nama' => $this->pelanggan->nama
                ];
            }),
            'items' => $this->whenLoaded('details', function() {
                return $this->details->map(function($detail) {

                    $produk = $detail->produk;
                    $namaLengkap = 'Produk Terhapus';

                    if ($produk) {
                        $namaBahan = $produk->bahan ? $produk->bahan->nama : '';
                        $warna = $produk->warna ?? '';

                        $namaLengkap = trim($produk->nama . ' ' . $warna . ' ' . $namaBahan);
                    }
                    return [
                        'produk' => $namaLengkap,
                        'size' => $detail->size->tipe ?? '-',
                        'jumlah' => $detail->jumlah,
                        'harga_satuan' => $detail->hargaProduk,
                        'subtotal' => $detail->jumlah * $detail->hargaProduk
                    ];
                });
            }),
        ];
    }
}
