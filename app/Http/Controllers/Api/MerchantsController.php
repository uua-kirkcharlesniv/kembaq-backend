<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Merchant;
use App\Models\Message;
use App\Models\Reward;
use App\Models\Subscription;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MerchantsController extends Controller
{
    public function getAllMerchants() {
        return response()->json(['merchants' => Merchant::all()]);
    }

    public function fetchAllSubscribedMerchants() {
        return response()->json(['subscribed' => User::with('subscriptions', 'subscriptions.merchant', 'subscriptions.merchant.rewards')->findOrFail(Auth::user()->id)['subscriptions']]);
    }

    public function fetchAllAvailableMerchants() {
        $subscriptions = User::with('subscriptions')->findOrFail(Auth::user()->id)['subscriptions'];
        $subscribedIds = collect($subscriptions)->map(function ($subscription) {
            return $subscription->merchant_id;
        });
        
        return response()->json(['available' =>  Merchant::whereNotIn('id', $subscribedIds)->get()]);
    }

    public function getMerchantMessages(Request $request, $id) {
        return response()->json(['messages' =>  Message::where('merchant_id', $id)->get()]);
    }

    public function getMerchantRewards(Request $request, $id) {
        return response()->json(['rewards' => Reward::where('merchant_id', $id)->get()]);
    }

    public function getAllRewards() {
        $subscriptions = User::with('subscriptions')->findOrFail(Auth::user()->id)['subscriptions'];
        $subscribedIds = collect($subscriptions)->map(function ($subscription) {
            return $subscription->merchant_id;
        });
        
        return response()->json(['rewards' => Reward::with('merchant')->whereIn('merchant_id', $subscribedIds)->get()]);
    }
}
