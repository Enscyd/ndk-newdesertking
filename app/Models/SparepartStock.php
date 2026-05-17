<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SparepartStock extends Model
{
    protected $table = 'sparepart_stock';

    // ❗ Disable timestamps (important for Prisma tables)
    public $timestamps = false;

    protected $fillable = [
        'sparepart_id',
        'supplier_id',
        'type',
        'quantity',
        'note'
    ];

    /**
     * Relationship: Stock belongs to Sparepart
     */
    public function sparepart()
    {
        return $this->belongsTo(Sparepart::class, 'sparepart_id');
    }

    /**
     * Relationship: Stock belongs to Supplier
     */
    public function supplier()
    {
        return $this->belongsTo(SparepartSupplier::class, 'supplier_id');
    }
}