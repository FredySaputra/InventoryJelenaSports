<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Size extends Model
{
    protected $table = 'sizes';
    protected $primaryKey = 'id';
    protected $keyType = 'string';
    public $incrementing = false;
    public $timestamps = false;
    protected $fillable = [
        'tipe',
        'panjang',
        'lebar'
    ];

    public function stok() : HasMany
    {
        return $this->hasMany(Stok::class, 'idSize', 'id');
    }
}
