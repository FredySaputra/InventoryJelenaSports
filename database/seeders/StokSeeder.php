<?php

namespace Database\Seeders;

use App\Models\Produk;
use App\Models\Size;
use App\Models\Stok;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class StokSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $produks = Produk::with('kategori')->get();

        foreach ($produks as $produk) {

            $prefix = $produk->kategori->prefix_size ?? null;

            $sizes = collect();

            if ($prefix) {
                $sizes = Size::where('id', 'like', $prefix . '%')->get();
            } else {
                $sizes = Size::inRandomOrder()->limit(5)->get();
            }

            foreach ($sizes as $size) {
                Stok::updateOrCreate(
                    [
                        'idProduk' => $produk->id,
                        'idSize'   => $size->id
                    ],
                    [
                        'stok' => rand(10, 50)
                    ]
                );
            }
        }
    }
}
