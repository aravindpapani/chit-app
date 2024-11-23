<?php


namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $table = 'users';

    protected $primaryKey = 'id';

    public $incrementing = true;

    // The attributes that are mass assignable.
    protected $fillable = [
        'mobile_number',
        'first_name',
        'last_name',
        'email',
        'password',
        'otp',
        'is_verified',
        'otp_expires_at' 
    ];

    protected $hidden = [
        // 'otp',
        'password',
        // 'remember_token',
    ];

    // The attributes that should be cast to native types.
    protected $casts = [
        'is_verified' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

}
