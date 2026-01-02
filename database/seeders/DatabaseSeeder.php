<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        User::create([
            'id' => 'ADM1',
            'nama' => 'Admin 1',
            'noTelp' => '085248239843',
            'username' => 'admin',
            'password' => Hash::make('rahasia'),
            'role' => 'Admin'
        ]);

    }
}
