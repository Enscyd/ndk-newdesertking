<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SparePartCategory extends Model
{
    protected $table = 'sparepart_category';
    public $timestamps = false;

    protected $fillable = [
        'name',
        'description'
    ];

    public function spareparts()
    {
        return $this->hasMany(SparePart::class, 'category_id');
    }
}
