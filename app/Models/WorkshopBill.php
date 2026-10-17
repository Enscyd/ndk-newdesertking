<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WorkshopBill extends Model
{
    protected $table = 'workshopbills';
    public $timestamps = false;

    protected $fillable = [
        'bill_no',
        'vehicle_no',
        'name',
        'date',
        'payment_status',
        'total_amount'
    ];

    protected $casts = [
        'total_amount' => 'decimal:3',
    ];

    public function items()
    {
        return $this->hasMany(WorkshopItem::class, 'billId', 'id');
    }
}