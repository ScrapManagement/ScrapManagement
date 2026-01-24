<?php

namespace App\Models\Product;

use App\Models\User;
use App\Models\Product\Order;
use App\Models\Product\Product;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class OrderItem extends Model
{
    use HasFactory, Notifiable, SoftDeletes;
    protected $fillable = [
        'order_id',
        'product_id',
        'seller_id',
        'quantity',
        'price',
    ];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function seller()
    {
        return $this->belongsTo(User::class, 'seller_id');
    }
}
