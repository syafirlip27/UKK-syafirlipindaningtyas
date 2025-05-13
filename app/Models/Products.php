<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

class Products extends Model
{
        use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'price',
        'stock',
        'image'
    ];

    public function detail_sales() 
    {
        return $this->hasMany(Detail_sales::class, 'product_id', 'id');
    }
}
