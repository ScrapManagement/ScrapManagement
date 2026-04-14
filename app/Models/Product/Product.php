<?php

namespace App\Models\Product;

use App\Models\Admin\Admin;
use App\Models\Auction\Auction;
use App\Models\Payment\ProductUnlock;
use App\Models\Product\Category;
use App\Models\Product\Image;
use App\Models\User\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Notifications\Notifiable;

class Product extends Model
{
    use HasFactory, Notifiable, SoftDeletes;

    protected $fillable = [
        'user_id',
        'category_id',
        'name',
        'description',
        'quantity',
        'unit',
        'price',
        'status',
        'material_priority',
        'reviewed_by',
        'sale_type',
    ];

    public function seller()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function images()
    {
        return $this->hasMany(Image::class);
    }

    public function reviewer()
    {
        return $this->belongsTo(Admin::class, 'reviewed_by');
    }

    public function favoredBy()
    {
        return $this->belongsToMany(User::class, 'favorites')->withTimestamps();
    }

    public function productUnlocks()
    {
        return $this->hasMany(ProductUnlock::class);
    }

    public function isUnlockedBy($userId)
    {
        if (!$userId) return false;
        return $this->productUnlocks()->where('user_id', $userId)->exists();
    }

    /** المزاد المرتبط بالمنتج (واحد بس) */
    public function auction()
    {
        return $this->hasOne(Auction::class);
    }

    public function isAuction(): bool
    {
        return $this->sale_type === 'auction';
    }

    public function isCoins(): bool
    {
        return $this->sale_type === 'coins';
    }

    public function isApproved(): bool
    {
        return $this->status === 'approved';
    }
}
