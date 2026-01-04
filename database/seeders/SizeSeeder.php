<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Size;

class SizeSeeder extends Seeder
{
    public function run(): void
    {
        $sizes = [
            ['id' => 'BJU-001', 'tipe' => '5'],
            ['id' => 'BJU-002', 'tipe' => '6'],
            ['id' => 'BJU-003', 'tipe' => '7'],
            ['id' => 'BJU-004', 'tipe' => '8'],
            ['id' => 'BJU-005', 'tipe' => '9'],
            ['id' => 'BJU-006', 'tipe' => 'S'],
            ['id' => 'BJU-007', 'tipe' => 'M'],
            ['id' => 'BJU-008', 'tipe' => 'L'],
            ['id' => 'BJU-009', 'tipe' => 'XL'],
            ['id' => 'BJU-010', 'tipe' => 'XXL'],
            ['id' => 'BJU-011', 'tipe' => '3XL'],
            ['id' => 'BJU-012', 'tipe' => '4XL'],

            ['id' => 'SBK-001', 'tipe' => 'PT'],
            ['id' => 'SBK-002', 'tipe' => 'KN'],
            ['id' => 'SBK-003', 'tipe' => 'OR'],
            ['id' => 'SBK-004', 'tipe' => 'MR'],
            ['id' => 'SBK-005', 'tipe' => 'HJ'],
            ['id' => 'SBK-006', 'tipe' => 'BR'],
            ['id' => 'SBK-007', 'tipe' => 'BB'],
            ['id' => 'SBK-008', 'tipe' => 'CO'],
            ['id' => 'SBK-009', 'tipe' => 'HI'],
            ['id' => 'SBK-010', 'tipe' => 'PTKN'],
            ['id' => 'SBK-011', 'tipe' => 'KNHJ'],
            ['id' => 'SBK-012', 'tipe' => 'MRHT'],
        ];

        foreach ($sizes as $s) {
            Size::updateOrCreate(['id' => $s['id']], $s);
        }
    }
}
