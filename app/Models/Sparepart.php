<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SparePart extends Model
{
    protected $table = 'sparepart';

    protected $fillable = [
        'name',
        'part_number',
        'category_id'
    ];

    public function category()
    {
        return $this->belongsTo(SparePartCategory::class, 'category_id');
    }

    public function stocks()
    {
        return $this->hasMany(SparePartStock::class, 'sparepart_id');
    }
}
