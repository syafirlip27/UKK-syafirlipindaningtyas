<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

class Detail_sales extends Model
{
        use HasFactory, Notifiable;

    protected $fillable = [
        'sale_id',
        'product_id',
        'amount',
        'subtotal'
    ];
    public function sales() 
    {
        return $this->belongsTo(Sales::class);
    }
    public function product() 
    {
        return $this->belongsTo(Products::class);
    }
}
