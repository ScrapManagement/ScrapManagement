<?php

namespace App\Models\Payment;

use App\Models\Payment\Package;
use App\Models\User\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
   use HasFactory;

    protected $fillable = [
        'user_id',
        'package_id',
        'gateway',
        'order_id',
        'amount',
        'status',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
    ];

 

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function package()
    {
        return $this->belongsTo(Package::class);
    }



    public function isPaid()
    {
        return $this->status === 'paid';
    }

    public function isPending()
    {
        return $this->status === 'pending';
    }

    public function markAsPaid()
    {
        $this->update(['status' => 'paid']);
    }

    public function markAsFailed()
    {
        $this->update(['status' => 'failed']);
    }
}
