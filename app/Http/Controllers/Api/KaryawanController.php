<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;

// --- PASTIKAN 3 BARIS INI ADA ---
use App\Http\Resources\KaryawanResource;       // <--- PENTING: Penyebab error 500 jika hilang
use App\Http\Requests\StoreKaryawanRequest;
use App\Http\Requests\UpdateKaryawanRequest;
// --------------------------------

use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class KaryawanController extends Controller
{
    public function index()
    {
        $karyawan = User::where('role', 'Karyawan')
            ->orderBy('id', 'desc')
            ->get();

        return KaryawanResource::collection($karyawan);
    }

    public function store(StoreKaryawanRequest $request)
    {
        return DB::transaction(function () use ($request) {

            $lastUser = User::where('id', 'like', 'KRY-%')->orderBy('id', 'desc')->first();

            if (!$lastUser) {
                $newId = 'KRY-001';
            } else {
                $lastNumber = (int) substr($lastUser->id, 4);
                $newNumber = $lastNumber + 1;
                $newId = 'KRY-' . str_pad($newNumber, 3, '0', STR_PAD_LEFT);
            }

            $user = User::create([
                'id'       => $newId,
                'nama'     => $request->nama,
                'noTelp'   => $request->noTelp,
                'username' => $request->username,
                'password' => Hash::make($request->password),
                'role'     => 'Karyawan'
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Karyawan berhasil ditambahkan',
                'data'    => new KaryawanResource($user)
            ], 201);
        });
    }

    public function update(UpdateKaryawanRequest $request, $id)
    {
        $user = User::where('id', $id)->where('role', 'Karyawan')->firstOrFail();

        $data = [
            'nama'     => $request->nama,
            'noTelp'   => $request->noTelp,
            'username' => $request->username,
        ];

        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        $user->update($data);

        return response()->json([
            'success' => true,
            'message' => 'Data karyawan berhasil diupdate',
            'data'    => new KaryawanResource($user)
        ]);
    }

    public function destroy($id)
    {
        $user = User::where('id', $id)->where('role', 'Karyawan')->firstOrFail();
        $user->delete();

        return response()->json([
            'success' => true,
            'message' => 'Karyawan berhasil dihapus'
        ]);
    }
}
