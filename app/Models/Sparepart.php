<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Sparepart extends Model
{
    protected $table = 'sparepart';

    protected $fillable = [
        'name',
        'part_number',
        'category_id'
    ];

    public function category()
    {
        return $this->belongsTo(SparepartCategory::class, 'category_id');
    }

    public function stocks()
    {
        return $this->hasMany(SparepartStock::class, 'sparepart_id');
    }
}
