<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DetailTransaksi extends Model
{
    protected $table = 'detail_transaksis';
    public $timestamps = false;
    protected $guarded = [];
    public function produk() : BelongsTo
    {
        return $this->belongsTo(Produk::class,'idProduk','id');
    }

    public function transaksi() : BelongsTo
    {
        return $this->belongsTo(Transaksi::class,'idTransaksi','id');
    }

    public function size() : BelongsTo
    {
        return $this->belongsTo(Size::class, 'idSize', 'id');
    }

}
