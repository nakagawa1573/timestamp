<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Laravel\Fortify\TwoFactorAuthenticatable;
use Illuminate\Contracts\Auth\MustVerifyEmail;

class User extends Authenticatable implements MustVerifyEmail
{
    use HasApiTokens, HasFactory, Notifiable, TwoFactorAuthenticatable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'status',
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

    public function work()
    {
        return $this->hasMany(Work::class);
    }

    public function rest()
    {
        return $this->hasMany(Rest::class);
    }

    public function scopeStatusSearch($query, $status){
        if ($status !== null && $status !== '全て') {
            $query->where('status', $status)->get();
        }
    }

    public function scopeNameSearch($query, $keyword) {
        $query->where('name', 'LIKE', '%' . $keyword . '%')->get();
    }

    public function scopeDateSearch($query, $date)
    {
        if (!empty($date)) {
            $query->whereDate('', '=', $date)->get();
        }
    }
}
