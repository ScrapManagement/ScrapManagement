<?php

namespace App\Models\Admin;

use App\Models\Product\Product;
use App\Models\Product\Category;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class Admin extends Model
{
    use HasFactory, Notifiable, SoftDeletes;

    protected $fillable = [
        'name',
        'email',
        'phone',
        'password'
    ];

    public function categories()
    {
        return $this->hasMany(Category::class, 'created_by');
    }

    public function reviewedProducts()
    {
        return $this->hasMany(Product::class, 'reviewed_by');
    }

    protected $hidden = [
        'password',
    ];

    protected function casts(): array
    {
        return [
            'password' => 'hashed',
        ];
    }
}
