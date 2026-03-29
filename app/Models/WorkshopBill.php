<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WorkshopBill extends Model
{
    protected $table = 'WorkshopBills';
    public $timestamps = false;

    protected $fillable = [
        'bill_no',
        'vehicle_no',
        'name',
        'date',
        'payment_status',
        'total_amount'
    ];

    public function items()
    {
        return $this->hasMany(WorkshopItem::class, 'billId', 'id');
    }
}