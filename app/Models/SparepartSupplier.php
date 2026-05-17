<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SparePartSupplier extends Model
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
        return $this->hasMany(SparePartStock::class, 'supplier_id');
    }
}
