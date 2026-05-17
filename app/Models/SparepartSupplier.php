<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SparepartSupplier extends Model
{
    protected $table = 'sparepart_supplier';

    // ❗ Disable timestamps (important for Prisma tables)
    public $timestamps = false;

    protected $fillable = [
        'name',
        'phone',
        'address'
    ];

    /**
     * Relationship: Supplier has many stock entries
     */
    public function stocks()
    {
        return $this->hasMany(SparepartStock::class, 'supplier_id');
    }
}