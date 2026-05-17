<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SparepartCategory extends Model
{
    protected $table = 'sparepart_category';

    // ❗ Disable timestamps (IMPORTANT for Prisma)
    public $timestamps = false;

    protected $fillable = [
        'name',
        'description'
    ];

    /**
     * Relationship: Category has many Spareparts
     */
    public function spareparts()
    {
        return $this->hasMany(Sparepart::class, 'category_id');
    }
}