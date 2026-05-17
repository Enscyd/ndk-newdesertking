<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WorkshopItem extends Model
{
    protected $table = 'workshopitems';
    public $timestamps = false;

    protected $fillable = [
        'description',
        'price',
        'billId'
    ];

    public function bill()
    {
        return $this->belongsTo(WorkshopBill::class, 'billId', 'id');
    }
}