<?php

namespace App\Models\Auction;

use App\Models\Auction\Auction;
use App\Models\Payment\Payment;
use App\Models\User\User;
use Illuminate\Database\Eloquent\Model;

class AuctionParticipant extends Model
{
    protected $fillable = [
        'auction_id',
        'user_id',
        'insurance_amount',
        'insurance_status',
        'inspection_type',
        'inspection_status',
    ];

    protected $casts = [
        'insurance_amount' => 'decimal:2',
    ];

    // ──────────────────────────────────────────
    // Relationships
    // ──────────────────────────────────────────

    public function auction()
    {
        return $this->belongsTo(Auction::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // ──────────────────────────────────────────
    // Helpers
    // ──────────────────────────────────────────

    /**
     * رجّع التأمين للخاسر
     */
    public function refundInsurance(): void
    {
        // 1. رجّع الفلوس للمشتري (عبر payment gateway أو wallet)
        // 2. حدّث الـ status
        $this->update(['insurance_status' => 'refunded']);

        // سجّل coin transaction لو النظام بيستخدم coins
        // CoinTransaction::create([...]);
    }

    /**
     * اخصم التأمين من سعر المنتج للفايز الجاد
     */
    public function deductFromFinalPrice(): void
    {
        $this->update(['insurance_status' => 'deducted']);
    }

    /**
     * صادر التأمين من الفايز المش جاد
     */
    public function forfeitInsurance(): void
    {
        $this->update(['insurance_status' => 'forfeited']);
    }

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }
}
