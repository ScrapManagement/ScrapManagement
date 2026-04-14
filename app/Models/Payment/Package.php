<?php

namespace App\Models\Payment;

use App\Interfaces\PayableInterface;
use App\Models\Payment\CoinTransaction;
use App\Models\Payment\Payment;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

class Package extends Model implements PayableInterface
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'price',
        'coins',
        'is_active',
    ];

    protected $casts = [
        'price'     => 'decimal:2',
        'is_active' => 'boolean',
    ];

    public function coinTransactions()
    {
        return $this->morphMany(CoinTransaction::class, 'reference');
    }

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }

    public function getAmount(): float {
        return $this->price;
    }

    public function getPayableId(): int {
        return $this->id;
    }

    public function getPaymentType(): string {
        return 'package';
    }
}
