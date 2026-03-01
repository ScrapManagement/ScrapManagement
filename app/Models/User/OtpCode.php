<?php

namespace App\Models\User;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Factories\HasFactory;


class OtpCode extends Model
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'user_id',
        'code',
        'expires_at',
        'verified_at',
    ];

    protected $casts = [
        'expires_at' => 'datetime',
        'verified_at' => 'datetime',
        'code' => 'hashed',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
