<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Produk;
use App\Models\User;

class ProdukSeeder extends Seeder
{
    public function run(): void
    {

        $user = User::first();


        if (!$user) {
            $user = User::create([
                'nama' => 'Administrator',
                'username' => 'admin',
                'password' => bcrypt('password'),
                'role' => 'Admin',
                'noTelp' => '08123456789'
            ]);
        }

        $adminId = $user->id;

        $data = [
            [
                'id' => 'KARATE-TP',
                'nama' => 'Baju Karate TP',
                'warna' => null,
                'idKategori' => 'KAT-01',
                'idUser' => $adminId
            ],
            [
                'id' => 'KARATE-DRILL',
                'nama' => 'Baju Karate Drill',
                'warna' => null,
                'idKategori' => 'KAT-01',
                'idUser' => $adminId
            ],
            [
                'id' => 'KARATE-TEBAL',
                'nama' => 'Baju Karate Tebal',
                'warna' => null,
                'idKategori' => 'KAT-01',
                'idUser' => $adminId
            ],
            // --- BAJU TAEKWONDO (PUTIH) ---
            [
                'id' => 'TKD-TP',
                'nama' => 'Baju Taekwondo TP',
                'warna' => null,
                'idKategori' => 'KAT-01',
                'idUser' => $adminId
            ],
            [
                'id' => 'TKD-DRILL',
                'nama' => 'Baju Taekwondo Drill',
                'warna' => null,
                'idKategori' => 'KAT-01',
                'idUser' => $adminId
            ],
            [
                'id' => 'TKD-TEBAL',
                'nama' => 'Baju Taekwondo Tebal',
                'warna' => null,
                'idKategori' => 'KAT-01',
                'idUser' => $adminId
            ],
            // --- BAJU TAEKWONDO (MERAH HITAM) ---
            [
                'id' => 'TKD-MH-TP',
                'nama' => 'Baju Taekwondo Merah Hitam TP',
                'warna' => 'Merah Hitam',
                'idKategori' => 'KAT-01',
                'idUser' => $adminId
            ],
            [
                'id' => 'TKD-MH-DRILL',
                'nama' => 'Baju Taekwondo Merah Hitam Drill',
                'warna' => 'Merah Hitam',
                'idKategori' => 'KAT-01',
                'idUser' => $adminId
            ],
            [
                'id' => 'TKD-MH-TEBAL',
                'nama' => 'Baju Taekwondo Merah Hitam Tebal',
                'warna' => 'Merah Hitam',
                'idKategori' => 'KAT-01',
                'idUser' => $adminId
            ],
            // --- BAJU SILAT ---
            [
                'id' => 'SILAT-TP',
                'nama' => 'Baju Silat TP',
                'warna' => null,
                'idKategori' => 'KAT-01',
                'idUser' => $adminId
            ],
            [
                'id' => 'SILAT-DRILL',
                'nama' => 'Baju Silat Drill',
                'warna' => null,
                'idKategori' => 'KAT-01',
                'idUser' => $adminId
            ],
            [
                'id' => 'SILAT-TEBAL',
                'nama' => 'Baju Silat Tebal',
                'warna' => null,
                'idKategori' => 'KAT-01',
                'idUser' => $adminId
            ],
            // --- SABUK ---
            [
                'id' => 'SABUK-BIASA',
                'nama' => 'Sabuk Biasa',
                'warna' => null,
                'idKategori' => 'KAT-02',
                'idUser' => $adminId
            ],
            [
                'id' => 'SABUK-BORDIR',
                'nama' => 'Sabuk Tebal Bordir',
                'warna' => null,
                'idKategori' => 'KAT-02',
                'idUser' => $adminId
            ],
            [
                'id' => 'SABUK-SILAT',
                'nama' => 'Sabuk Silat',
                'warna' => null,
                'idKategori' => 'KAT-02',
                'idUser' => $adminId
            ],
        ];

        foreach ($data as $item) {
            Produk::updateOrCreate(['id' => $item['id']], $item);
        }
    }
}
