<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Account extends Model
{
    protected $fillable = [
        'purposeId',
        'description',
        'type',
        'amount',
        'date'
    ];

    public $timestamps = false;

    // Relation
    public function purpose()
    {
        return $this->belongsTo(Purpose::class, 'purposeId');
    }
}