<?php

namespace App\Models\Auction;

use App\Interfaces\PayableInterface;
use App\Models\Auction\AuctionBid;
use App\Models\Auction\AuctionParticipant;
use App\Models\Payment\Payment;
use App\Models\Product\Product;
use App\Models\User\User;
use Illuminate\Database\Eloquent\Model;

class Auction extends Model implements PayableInterface
{
     protected $fillable = [
        'product_id',
        'winner_id',
        'status',
        'starting_price',
        'current_price',
        'insurance_rate',
        'starts_at',
        'ends_at',
    ];

    protected $casts = [
        'starting_price'  => 'decimal:2',
        'current_price'   => 'decimal:2',
        'insurance_rate'  => 'decimal:2',
        'starts_at'       => 'datetime',
        'ends_at'         => 'datetime',
    ];

    // ──────────────────────────────────────────
    // Relationships
    // ──────────────────────────────────────────

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function winner()
    {
        return $this->belongsTo(User::class, 'winner_id');
    }

    public function participants()
    {
        return $this->hasMany(AuctionParticipant::class);
    }

    public function bids()
    {
        return $this->hasMany(AuctionBid::class);
    }

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }

    // ──────────────────────────────────────────
    // Scopes
    // ──────────────────────────────────────────

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeScheduled($query)
    {
        return $query->where('status', 'scheduled');
    }

    // ──────────────────────────────────────────
    // Helpers
    // ──────────────────────────────────────────

    /**
     * حساب مبلغ التأمين بناءً على سعر المنتج
     */
    public function calculateInsurance(): float
    {
        return round($this->starting_price * ($this->insurance_rate / 100), 2);
    }

    /**
     * هل المزاد فاتح دلوقتي؟
     */
    public function isActive(): bool
    {
        return $this->status === 'active';
    }

    /**
     * هل اليوزر ده مشارك في المزاد؟
     */
    public function hasParticipant(int $userId): bool
    {
        return $this->participants()->where('user_id', $userId)->exists();
    }

    /**
     * آخر bid وأعلاها
     */
    public function highestBid(): ?AuctionBid
    {
        return $this->bids()->orderByDesc('amount')->first();
    }

    public function getAmount(): float {
        return $this->calculateInsurance();
    }

    public function getPayableId(): int {
        return $this->id;
    }

    public function getPaymentType(): string {
        return 'insurance';
    }
}
