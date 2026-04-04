<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $table = 'products';

    protected $fillable = [
        'name',
        'description',
        'price',
        'stock',
    ];

    protected $hidden = [
        // Add any hidden fields if needed
    ];

    public $timestamps = true;
}