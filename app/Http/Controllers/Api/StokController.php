<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Produk;
use App\Models\Size;
use App\Models\Stok;
use App\Models\Kategori;
use App\Http\Requests\UpdateStokRequest;
use App\Http\Resources\StokResource;

class StokController extends Controller
{
    public function index()
    {
        $kategoris = Kategori::orderBy('nama')->get();
        $dataMatrix = [];

        foreach ($kategoris as $cat) {
            if (!$cat->prefix_size) continue;

            $sizes = Size::where('id', 'like', $cat->prefix_size . '%')
                ->orderBy('id', 'asc')
                ->get();

            $produks = Produk::with(['stoks'])
                ->where('idKategori', $cat->id)
                ->orderBy('nama')
                ->get();

            if ($produks->count() > 0) {
                $dataMatrix[] = [
                    'kategori_nama' => $cat->nama,
                    'sizes' => $sizes,
                    'produks' => $produks
                ];
            }
        }

        return response()->json($dataMatrix);
    }

    public function update(UpdateStokRequest $request)
    {
        $validated = $request->validated();

        $stok = Stok::updateOrCreate(
            [
                'idProduk' => $validated['idProduk'],
                'idSize' => $validated['idSize']
            ],
            [
                'stok' => $validated['jumlah']
            ]
        );

        return new StokResource($stok);
    }
}
