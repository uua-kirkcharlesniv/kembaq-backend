<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'first_name',
        'last_name',
        'email',
        'password',
        'photo',
        'is_merchant'
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
        'email_verified_at' => 'datetime'
    ];

    protected $appends = ['name', 'is_merchant_profile_created', 'merchant_id'];

    public function getNameAttribute() {
        return $this->first_name . ' ' . $this->last_name;
    }

    public function merchants() {
        return $this->belongsToMany(Merchant::class, 'merchant_user', 'user_id', 'merchant_id')->withPivot('role');
    }

    public function subscriptions() {
        return $this->hasMany(Subscription::class);
    }

    public function getIsMerchantAttribute($value) {
        return $value == 1;
    }

    public function getIsMerchantProfileCreatedAttribute() {
        if($this->is_merchant == 0) return false;

        return $this->merchants()->count() > 0;
    }

    public function getMerchantIdAttribute() {
        if($this->getIsMerchantProfileCreatedAttribute()) {
            return $this->merchants()->first()->id;
        }

        return null;
    }
}
