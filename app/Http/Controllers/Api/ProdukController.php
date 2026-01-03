<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Produk;
use App\Models\Kategori;
use App\Http\Requests\StoreProdukRequest;
use App\Http\Resources\ProdukResource;
use App\Http\Resources\KategoriWithProdukResource; // <--- WAJIB ADA
use Illuminate\Http\Request;

class ProdukController extends Controller
{
    public function index()
    {
        // Ambil kategori beserta produknya
        $kategoris = Kategori::with(['produks' => function($query) {
            $query->orderBy('nama', 'asc');
        }])->orderBy('nama', 'asc')->get();

        // Return menggunakan resource pembungkus kategori
        return KategoriWithProdukResource::collection($kategoris);
    }

    public function store(StoreProdukRequest $request)
    {
        $data = $request->validated();
        $data['idUser'] = auth()->guard('api')->id() ?? 'Admin';

        // Buang idBahan dari array data sebelum create karena tabel produks tidak punya kolom idBahan
        // (Tapi tetap divalidasi di Request agar dropdown wajib dipilih)
        if(isset($data['idBahan'])) {
            unset($data['idBahan']);
        }

        $produk = Produk::create($data);

        return new ProdukResource($produk);
    }

    public function show($id)
    {
        $produk = Produk::with(['kategori', 'stoks'])->find($id);
        if (!$produk) return response()->json(['message' => 'Produk tidak ditemukan'], 404);
        return new ProdukResource($produk);
    }

    public function update(Request $request, $id)
    {
        $produk = Produk::find($id);
        if (!$produk) return response()->json(['message' => 'Produk tidak ditemukan'], 404);

        $request->validate([
            'nama' => 'required|string|max:100',
            'warna' => 'nullable|string|max:50',
            'idKategori' => 'required|exists:kategoris,id',
        ]);

        $produk->update($request->all());
        return new ProdukResource($produk);
    }

    public function destroy($id)
    {
        $produk = Produk::find($id);
        if (!$produk) return response()->json(['message' => 'Produk tidak ditemukan'], 404);
        $produk->delete();
        return response()->json(['message' => 'Produk berhasil dihapus']);
    }
}
