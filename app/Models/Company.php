<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Company extends Model
{
    use HasFactory;

    /**
     * Prisma creates a table named "Company".
     */
    protected $table = 'Companies';

    /**
     * Allow mass assignment for the 'name' field.
     */
    protected $fillable = [
        'name',
    ];

    /**
     * Disable Laravel's timestamps because Prisma model has no createdAt/updatedAt.
     */
    public $timestamps = false;
}