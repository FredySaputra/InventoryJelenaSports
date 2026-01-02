<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DetailTransaksi extends Model
{
    protected $table = 'detail_transaksis';
    protected $primaryKey = ['idTransaksi','idProduk'];
    public $timestamps = false;
    public $incrementing = false;
    protected $fillable = [
        'harga',
        'jumlah'
    ];
    public function produk() : BelongsTo
    {
        return $this->belongsTo(Produk::class,'idProduk','id');
    }

    public function transaksi() : BelongsTo
    {
        return $this->belongsTo(Transaksi::class,'idTransaksi','id');
    }

}
