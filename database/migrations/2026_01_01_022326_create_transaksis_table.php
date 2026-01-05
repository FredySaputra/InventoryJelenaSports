<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('transaksis', function (Blueprint $table) {
            $table->string('id', 100)->primary();
            $table->date('tanggalTransaksi')->nullable(false);
            $table->decimal('totalTransaksi',15,2)->default(0)->nullable(false);
            $table->integer('totalItem')->default(0);
            $table->string('idPelanggan')->nullable(false);
            $table->timestamps();

            $table->foreign('idPelanggan')->on('pelanggans')->references('id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transaksis');
    }
};
