<?php

namespace App\Models\Payment;

use App\Models\User\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

class CoinTransaction extends Model
{
    use HasFactory , Notifiable;

    protected $fillable = [
        'user_id',
        'type',
        'amount',
        'reference_type',
        'reference_id',
    ];

    protected $casts = [
        'amount' => 'integer',
    ];


    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function reference()
    {
        return $this->morphTo();
    }



    public function isCredit()
    {
        return $this->type === 'credit';
    }

    public function isDebit()
    {
        return $this->type === 'debit';
    }
}
