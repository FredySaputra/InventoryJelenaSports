<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Size;
use App\Http\Resources\SizeResource;
use App\Http\Requests\StoreSizeRequest;
use App\Http\Requests\UpdateSizeRequest;

class SizeController extends Controller
{
    public function index()
    {
        $sizes = Size::orderBy('id', 'asc')->get();
        return SizeResource::collection($sizes);
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
