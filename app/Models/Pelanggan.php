<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Pelanggan extends Model
{
    protected $table = 'pelanggans';
    protected $primaryKey = 'id';
    protected $keyType = 'string';
    public $incrementing = 'false';
    public $timestamps = 'false';

    protected $fillable = [
        'nama',
        'alamat',
        'kontak'
    ];

    public  function  transaksi():HasMany
    {
        return  $this->hasMany(Transaksi::class,'idPelanggan','id');
    }
}
