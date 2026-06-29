<?php

namespace App\Models\Product;

use App\Models\Admin\Admin;
use App\Models\Product\Product;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Category extends Model
{
    use HasFactory, Notifiable, SoftDeletes;

    protected $fillable = [
        'parent_id',
        'name',
        'material_priority',
        'created_by',
    ];

    public function products()
    {
        return $this->hasMany(Product::class);
    }

    public function admin()
    {
        return $this->belongsTo(Admin::class, 'created_by');
    }

    public function parent()
    {
        return $this->belongsTo(Category::class, 'parent_id');
    }

    public function children()
    {
        return $this->hasMany(Category::class, 'parent_id');
    }


    public function getMaterialPriorityAttribute($value)
    {
        if ($this->parent_id) {
            return $this->parent?->material_priority;
        }
        return $value;
    }
}
