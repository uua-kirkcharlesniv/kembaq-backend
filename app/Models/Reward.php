<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class Reward extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    protected $appends = ['is_subscribed', 'is_claimable', 'percentage_progress', 'current_balance'];

    public function getIsSubscribedAttribute() {
        if(!Auth::check()) {
            return false;
        }

        if(Subscription::where('user_id', Auth::user()->id)->where('merchant_id', $this->merchant_id)->exists()) {
            return true;
        }

        return false;
    }

    public function getCurrentBalanceAttribute() {
        if(!$this->is_subscribed) return 0;

        $subscription = Subscription::where('user_id', Auth::user()->id)->where('merchant_id', $this->merchant_id)->first();   
        
        if(!$subscription) return 0; 

        return $subscription->balance;
    }

    public function getPercentageProgressAttribute() {
        if(!$this->is_subscribed) return 0;

        $subscription = Subscription::where('user_id', Auth::user()->id)->where('merchant_id', $this->merchant_id)->first();   
        
        if(!$subscription) return 0; 

        return min(floor(($subscription->balance / $this->value) * 100), 100);
    }

    public function getIsClaimableAttribute() {
        return $this->percentage_progress >= 100;
    }

    public function merchant() {
        return $this->belongsTo(Merchant::class, 'merchant_id', 'id');
    }
}
