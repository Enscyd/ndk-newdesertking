<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Truck extends Model
{
    protected $table = 'Trucks';

    protected $fillable = [
        'truckNumber'
    ];

    public $timestamps = false;
}