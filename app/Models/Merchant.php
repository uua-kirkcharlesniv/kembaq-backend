<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Merchant extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    protected $appends = ['earliest_expiration', 'admin'];

    public function category() {
        return $this->belongsTo(Category::class, 'category', 'id');
    }

    public function rewards() {
        return $this->hasMany(Reward::class, 'merchant_id', 'id');
    }

    public function messages() {
        return $this->hasMany(Message::class, 'merchant_id', 'id');
    }
    
    public function getAdminAttribute() {
        return User::find(MerchantUser::where('merchant_id', $this->id)->where('role', 'admin')->first()->user_id);
    }

    public function getEarliestExpirationAttribute() {
        if($this->rewards->isEmpty()) return null;

        return Reward::where('merchant_id', $this->id)->orderBy('valid_until','ASC')->first()->valid_until;
    }

    public function getLogoAttribute($value) {
        if (filter_var($value, FILTER_VALIDATE_URL) === FALSE) {
            return asset('storage/'.$value);
        }

        return $value;
    }
    
    public function getHeroAttribute($value) {
        if (filter_var($value, FILTER_VALIDATE_URL) === FALSE) {
            return asset('storage/'.$value);
        }

        return $value;
    }

    public function payments() {
        return $this->hasMany(Payment::class, 'merchant_id', 'id');
    }
}
