<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class Reward extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    protected $appends = ['is_subscribed', 'is_claimable', 'percentage_progress', 'current_balance', 'last_redeemed'];

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

    public function getPhotoAttribute($value) {
        if($value == null || $value == '') {
            return 'https://reactnative-examples.com/wp-content/uploads/2022/02/default-loading-image.png';
        } else if (filter_var($value, FILTER_VALIDATE_URL) === FALSE) {
            return asset('storage/'.$value);
        }

        return $value;
    }

    public function getLastRedeemedAttribute() {
        $user = Auth::user();
        if($user->is_merchant == 0) {
            $claim = Claim::where(['user_id' => $user->id, 'reward_id' => $this->id])->first();
            if($claim != null) {
                return $claim->created_at;
            }
        }

        return null;
    }
}
