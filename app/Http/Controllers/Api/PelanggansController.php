<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Pelanggan;
use App\Http\Resources\PelangganResource;
use App\Http\Requests\CreatePelangganRequest;
use App\Http\Requests\UpdatePelangganRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class PelanggansController extends Controller
{
    public function index(): AnonymousResourceCollection
    {

        $pelanggans = Pelanggan::orderBy('nama', 'asc')->get();

        return PelangganResource::collection($pelanggans)->additional([
            'status' => 'success',
            'message' => 'List data pelanggan'
        ]);
    }

    public function store(CreatePelangganRequest $request): PelangganResource
    {
        $pelanggan = Pelanggan::create($request->validated());

        return (new PelangganResource($pelanggan))->additional([
            'status' => 'success',
            'message' => 'Pelanggan berhasil ditambahkan',
        ]);
    }

    public function show($id): PelangganResource|JsonResponse
    {
        $pelanggan = Pelanggan::find($id);

        if (!$pelanggan) {
            return response()->json([
                'status' => 'error',
                'message' => 'Data tidak ditemukan'
            ], 404);
        }

        return new PelangganResource($pelanggan);
    }

    public function update(UpdatePelangganRequest $request, $id): PelangganResource|JsonResponse
    {
        $pelanggan = Pelanggan::find($id);

        if (!$pelanggan) {
            return response()->json([
                'status' => 'error',
                'message' => 'Data tidak ditemukan'
            ], 404);
        }

        $pelanggan->update($request->validated());

        return (new PelangganResource($pelanggan))->additional([
            'status' => 'success',
            'message' => 'Data pelanggan berhasil diperbarui',
        ]);
    }

    public function destroy($id): JsonResponse
    {
        $pelanggan = Pelanggan::find($id);

        if (!$pelanggan) {
            return response()->json([
                'status' => 'error',
                'message' => 'Data tidak ditemukan'
            ], 404);
        }

        $pelanggan->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Pelanggan berhasil dihapus'
        ]);
    }
}
