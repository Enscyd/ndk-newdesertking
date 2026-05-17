<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SparePartStock extends Model
{
    protected $table = 'sparepart_stock';
    
    public $timestamps = false; // DB only has created_at
    
    const CREATED_AT = 'created_at';
    const UPDATED_AT = null;

    protected $fillable = [
        'sparepart_id',
        'supplier_id',
        'type',
        'quantity',
        'note'
    ];

    public function sparepart()
    {
        return $this->belongsTo(SparePart::class, 'sparepart_id');
    }

    public function supplier()
    {
        return $this->belongsTo(SparePartSupplier::class, 'supplier_id');
    }
}
