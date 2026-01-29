<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne; // <--- Tambahkan ini

class PerintahProduksi extends Model
{
    protected $table = 'perintah_produksis';

    protected $primaryKey = 'id';
    protected $keyType = 'string';
    public $incrementing = false;

    protected $guarded = [];

    public function pelanggan(): BelongsTo
    {
        return $this->belongsTo(Pelanggan::class, 'idPelanggan', 'id');
    }

    public function details(): HasMany
    {
        return $this->hasMany(DetailPerintahProduksi::class, 'idPerintahProduksi', 'id');
    }

    public function transaksi(): HasOne
    {
        return $this->hasOne(Transaksi::class, 'idPerintahProduksi', 'id');
    }
}