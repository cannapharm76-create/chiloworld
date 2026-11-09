<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'username',
        'google_id',
        'avatar',
        'name',
        'email',
        'mobile_code',
        'mobile_number',
        'whatsapp_code',
        'whatsapp_number',
        'profile_image',
        'password',
        'role',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    /**
     * Full URL of the profile image (falls back to the default avatar).
     */
    public function getProfileImageUrlAttribute(): string
    {
        if ($this->profile_image && file_exists(base_path('public/uploads/profile/'.$this->profile_image))) {
            return asset('public/uploads/profile/'.$this->profile_image);
        }
        if ($this->avatar) {
            return $this->avatar;
        }
        return asset('public/front/img/default-profile.png');
    }
}
