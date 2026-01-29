<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Cek apakah kolom sudah ada (untuk menghindari error duplicate column)
        if (!Schema::hasColumn('transaksis', 'idPerintahProduksi')) {
            Schema::table('transaksis', function (Blueprint $table) {
                // Tipe data String(50) sesuaikan dengan id di tabel perintah_produksis (SPK-XXX)
                $table->string('idPerintahProduksi', 50)->nullable()->after('idPelanggan');

                // Relasi Foreign Key (Opsional, menjaga integritas data)
                $table->foreign('idPerintahProduksi')
                      ->references('id')
                      ->on('perintah_produksis')
                      ->onDelete('set null');
            });
        }
    }

    public function down(): void
    {
        Schema::table('transaksis', function (Blueprint $table) {
            // Hapus FK dan Kolom jika rollback
            $table->dropForeign(['idPerintahProduksi']);
            $table->dropColumn('idPerintahProduksi');
        });
    }
};