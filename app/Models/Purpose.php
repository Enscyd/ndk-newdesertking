<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Purpose extends Model
{
    protected $table = 'purposes'; // ✅ lowercase (match DB)

    protected $fillable = [
        'name'
    ];

    public $timestamps = false;
}