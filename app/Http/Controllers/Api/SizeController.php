<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Size;
use App\Models\Kategori;
use App\Http\Resources\SizeResource;
use App\Http\Requests\StoreSizeRequest;
use App\Http\Requests\UpdateSizeRequest;

class SizeController extends Controller
{
    public function index()
    {
        $kategoris = Kategori::whereNotNull('prefix_size')->orderBy('nama')->get();

        $groupedData = [];
        $usedIds = [];

        foreach ($kategoris as $cat) {
            $sizes = Size::where('id', 'like', $cat->prefix_size . '%')
                ->orderBy('id', 'asc')
                ->get();

            foreach($sizes as $s) $usedIds[] = $s->id;

            $groupedData[] = [
                'kategori_nama' => $cat->nama,
                'prefix' => $cat->prefix_size,
                'sizes' => SizeResource::collection($sizes)
            ];
        }

        $otherSizes = Size::whereNotIn('id', $usedIds)->orderBy('id')->get();

        if ($otherSizes->count() > 0) {
            $groupedData[] = [
                'kategori_nama' => 'Lain-lain / Tanpa Kategori',
                'prefix' => '',
                'sizes' => SizeResource::collection($otherSizes)
            ];
        }

        return response()->json(['data' => $groupedData]);
    }

    public function store(StoreSizeRequest $request)
    {
        $size = Size::create($request->validated());

        return new SizeResource($size);
    }

    public function show($id)
    {
        $size = Size::find($id);

        if (!$size) {
            return response()->json(['message' => 'Size tidak ditemukan'], 404);
        }

        return new SizeResource($size);
    }

    public function update(UpdateSizeRequest $request, $id)
    {
        $size = Size::find($id);

        if (!$size) {
            return response()->json(['message' => 'Size tidak ditemukan'], 404);
        }

        $size->update($request->validated());

        return new SizeResource($size);
    }

    public function destroy($id)
    {
        $size = Size::find($id);

        if (!$size) {
            return response()->json(['message' => 'Size tidak ditemukan'], 404);
        }

        $size->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Size berhasil dihapus'
        ]);
    }
}
