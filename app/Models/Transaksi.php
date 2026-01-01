<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Transaksi extends Model
{
    protected $table = 'transaksis';
    protected $primaryKey = 'id';
    protected $keyType = 'string';
    public $incrementing = false;
    public $timestamps = false;

    protected $fillable = [
        'tanggalTransaksi',
        'totalTransaksi'
    ];

    public function detailTransaksi() : HasMany
    {
        return $this->hasMany(DetailTransaksi::class,'idTransaksi','id');
    }

    public function  pelanggan() : BelongsTo
    {
        return $this->belongsTo(Pelanggan::class,'idPelanggan','id');
    }
}
