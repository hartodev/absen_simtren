<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'company_id',
        'role',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function isSuperadmin(): bool
    {
        return $this->role === 'superadmin';
    }

   public function shiftGroups()
{
    return $this->belongsToMany(ShiftGroups::class,
        'shift_group_users',
        'user_id',
        'shift_group_id'
    )->withPivot(['start_date', 'end_date'])->withTimestamps();
}
}