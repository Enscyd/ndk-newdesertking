<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SparepartSupplier extends Model
{
    protected $table = 'sparepart_supplier';
    public $timestamps = false;

    protected $fillable = [
        'name',
        'phone',
        'address'
    ];

    public function stocks()
    {
        return $this->hasMany(SparepartStock::class, 'supplier_id');
    }
}
