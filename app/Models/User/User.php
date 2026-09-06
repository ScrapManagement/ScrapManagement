<?php

namespace App\Models\User;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Models\Auction\Auction;
use App\Models\Auction\AuctionBid;
use App\Models\Auction\AuctionParticipant;
use App\Models\Chatbot\ChatMessage;
use App\Models\Payment\CoinTransaction;
use App\Models\Payment\Payment;
use App\Models\Payment\ProductUnlock;
use App\Models\Product\Category;
use App\Models\Product\Order;
use App\Models\Product\Product;
use App\Models\User\Report;
use App\Models\User\Role;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Tymon\JWTAuth\Contracts\JWTSubject;

class User extends Authenticatable implements JWTSubject
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'phone',
        'city',
        'region',
        'coins',
        'category_id',
        'phone_verified_at',
        'address',
        'id_card_front',
        'id_card_back',
        'id_card_status',
        'id_card_verified_at',
        'account_type',
        'company',
        'job_title',
        'is_banned',
        'ban_reason',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'phone_verified_at' => 'datetime',
            'id_card_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }
    /**
     * Get the identifier that will be stored in the subject claim of the JWT.
     *
     * @return mixed
     */
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    /**
     * Return a key value array, containing any custom claims to be added to the JWT.
     *
     * @return array
     */
    public function getJWTCustomClaims()
    {
        return [
            'type' => 'user'
        ];
    }

    public function products()
    {
        return $this->hasMany(Product::class);
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }
    public function roles()
    {
        return $this->belongsToMany(Role::class, 'role_users');
    }

    public function isSeller()
    {
        return $this->roles()->where('name', 'seller')->exists();
    }

    public function isBuyer()
    {
        return $this->roles()->where('name', 'buyer')->exists();
    }

    public function orders()
    {
        return $this->hasMany(Order::class, 'buyer_id');
    }

    public function otps()
    {
        return $this->hasMany(OtpCode::class);
    }

    public function latestOtp()
    {
        return $this->hasOne(OtpCode::class)->latestOfMany();
    }

    public function coinTransactions()
    {
        return $this->hasMany(CoinTransaction::class);
    }

    public function unlockLogs()
    {
        return $this->hasMany(ProductUnlock::class);
    }

    public function unlockedProducts()
    {
        return $this->belongsToMany(Product::class, 'product_unlocks', 'user_id', 'product_id')
            ->withTimestamps();
    }

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }

    public function favoriteProducts()
    {
        return $this->belongsToMany(Product::class, 'favorites')->withTimestamps();
    }

    public function wonAuctions()
    {
        return $this->hasMany(Auction::class, 'winner_id');
    }

    public function auctionParticipations()
    {
        return $this->hasMany(AuctionParticipant::class);
    }

    public function auctionBids()
    {
        return $this->hasMany(AuctionBid::class);
    }

    public function isIdCardVerified(): bool
    {
        return $this->id_card_status === 'approved';
    }

    public function isIdCardPending(): bool
    {
        return $this->id_card_status === 'pending';
    }

    public function chatMessages()
    {
        return $this->hasMany(ChatMessage::class);
    }

    public function reportsReceived()
    {
        return $this->hasMany(Report::class, 'reported_user_id');
    }

    public function ban($reason)
    {
        $this->is_banned = true;
        $this->ban_reason = $reason;
        $this->save();
    }
}
