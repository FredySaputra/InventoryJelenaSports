<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Kategori;
use App\Models\Bahan;
use App\Http\Requests\StoreKategoriRequest;
use App\Http\Resources\KategoriResource;

class KategoriController extends Controller
{
    public function index()
    {
        $kategoris = Kategori::orderBy('nama')->get();
        return KategoriResource::collection($kategoris);
    }

    public function store(StoreKategoriRequest $request)
    {
        $kategori = Kategori::create($request->validated());
        return new KategoriResource($kategori);
    }

    public function show($id)
    {
        $kategori = Kategori::find($id);
        if (!$kategori) return response()->json(['message' => 'Tidak ditemukan'], 404);
        return new KategoriResource($kategori);
    }

    public function destroy($id)
    {
        if (Bahan::where('idKategori', $id)->exists()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Gagal! Kategori ini masih memiliki bahan. Hapus bahannya dulu.'
            ], 400);
        }

        $kategori = Kategori::find($id);
        if (!$kategori) return response()->json(['message' => 'Tidak ditemukan'], 404);

        $kategori->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Kategori berhasil dihapus'
        ]);
    }
}
