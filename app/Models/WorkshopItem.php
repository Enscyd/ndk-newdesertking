<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WorkshopItem extends Model
{
    protected $table = 'workshopitems';
    public $timestamps = false;

    protected $fillable = [
        'description',
        'qty',
        'rate',
        'price',
        'billId'
    ];

    protected $casts = [
        'qty' => 'decimal:2',
        'rate' => 'decimal:3',
        'price' => 'decimal:3',
    ];

    public function bill()
    {
        return $this->belongsTo(WorkshopBill::class, 'billId', 'id');
    }
}