<?php

namespace App\Models;

use App\Models\Order;
use App\Models\Product;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class OrderItem extends Model
{
    use HasFactory;

     protected $fillable               = [
      'order_id', 'product_id', 'price', 'quantity'
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function order()
    {
        return $this->belongsTo(Order  ::class);
    }   
    
    // public function review()
    // {
    //     return $this->hasOne(Review ::class,'order_item_id');
    // }
}