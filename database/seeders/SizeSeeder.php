<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Size;

class SizeSeeder extends Seeder
{
    public function run(): void
    {
        $sizes = [

            ['id' => 'BJU-001', 'tipe' => '5',   'panjang' => 50, 'lebar' => 38],
            ['id' => 'BJU-002', 'tipe' => '6',   'panjang' => 52, 'lebar' => 40],
            ['id' => 'BJU-003', 'tipe' => '7',   'panjang' => 54, 'lebar' => 42],
            ['id' => 'BJU-004', 'tipe' => '8',   'panjang' => 56, 'lebar' => 44],
            ['id' => 'BJU-005', 'tipe' => '9',   'panjang' => 58, 'lebar' => 46],

            ['id' => 'BJU-006', 'tipe' => 'S',   'panjang' => 65, 'lebar' => 48],
            ['id' => 'BJU-007', 'tipe' => 'M',   'panjang' => 68, 'lebar' => 50],
            ['id' => 'BJU-008', 'tipe' => 'L',   'panjang' => 71, 'lebar' => 52],
            ['id' => 'BJU-009', 'tipe' => 'XL',  'panjang' => 74, 'lebar' => 55],
            ['id' => 'BJU-010', 'tipe' => 'XXL', 'panjang' => 77, 'lebar' => 58],
            ['id' => 'BJU-011', 'tipe' => '3XL', 'panjang' => 80, 'lebar' => 61],
            ['id' => 'BJU-012', 'tipe' => '4XL', 'panjang' => 83, 'lebar' => 64],

            ['id' => 'SBK-001', 'tipe' => 'PT',   'panjang' => 260, 'lebar' => 4],
            ['id' => 'SBK-002', 'tipe' => 'KN',   'panjang' => 260, 'lebar' => 4],
            ['id' => 'SBK-003', 'tipe' => 'OR',   'panjang' => 260, 'lebar' => 4],
            ['id' => 'SBK-004', 'tipe' => 'MR',   'panjang' => 260, 'lebar' => 4],
            ['id' => 'SBK-005', 'tipe' => 'HJ',   'panjang' => 260, 'lebar' => 4],
            ['id' => 'SBK-006', 'tipe' => 'BR',   'panjang' => 260, 'lebar' => 4],
            ['id' => 'SBK-007', 'tipe' => 'BB',   'panjang' => 260, 'lebar' => 4],
            ['id' => 'SBK-008', 'tipe' => 'CO',   'panjang' => 280, 'lebar' => 4.5],
            ['id' => 'SBK-009', 'tipe' => 'HI',   'panjang' => 300, 'lebar' => 5],

            ['id' => 'SBK-010', 'tipe' => 'PTKN', 'panjang' => 260, 'lebar' => 4],
            ['id' => 'SBK-011', 'tipe' => 'KNHJ', 'panjang' => 260, 'lebar' => 4],
            ['id' => 'SBK-012', 'tipe' => 'MRHT', 'panjang' => 260, 'lebar' => 4],
        ];

        foreach ($sizes as $s) {
            Size::updateOrCreate(['id' => $s['id']], $s);
        }
    }
}
