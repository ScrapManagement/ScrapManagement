<?php

namespace App\Models\Payment;

use App\Models\Product\Product;
use App\Models\User\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

class ProductUnlock extends Model
{
    use HasFactory , Notifiable;

    protected $fillable = [
        'user_id',
        'product_id',
    ];



    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
