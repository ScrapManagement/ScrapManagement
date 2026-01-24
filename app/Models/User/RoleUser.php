<?php

namespace App\Models\User;

use App\Models\User;
use App\Models\User\Role;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class RoleUser extends Model
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'user_id',
        'role_id'
    ];

     public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }


    public function role()
    {
        return $this->belongsTo(Role::class, 'role_id');
    }
}
