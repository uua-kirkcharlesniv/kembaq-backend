<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Claim;
use App\Models\Merchant;
use App\Models\MerchantUser;
use App\Models\Notification;
use App\Models\Reward;
use App\Models\Subscription;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class RewardController extends Controller
{
    public function claimReward(Request $request) {
        $data = $request->validate([
            'user_id' => 'required|exists:users,id',
            'merchant_id' => 'required|exists:merchants,id',
            'reward_id' => 'required|exists:rewards,id',
        ]);

        return DB::transaction(function () use($data) {
            $subscription = Subscription::where('user_id', $data['user_id'])->where('merchant_id', $data['merchant_id'])->firstOrFail();
            $reward = Reward::where('merchant_id', $data['merchant_id'])->where('id', $data['reward_id'])->firstOrFail();
            $merchant = Merchant::findOrFail($data['merchant_id']);
            $user = User::findOrFail($data['user_id']);

            if($subscription->balance >= $reward->value) {
                Claim::create([
                    'merchant_id' => $data['merchant_id'],
                    'user_id' => $data['user_id'],
                    'reward_id' => $data['reward_id'],
                ]);
                DB::table('subscriptions')->where('user_id', '=', $data['user_id'])->where('merchant_id', '=', $data['merchant_id'])->decrement('balance', $reward->value);

                Notification::create([
                    'merchant_id' => $data['merchant_id'],
                    'user_id' => $data['user_id'],
                    'title' => 'Claimed a reward from ' . $merchant->business_name,
                    'message' => 'You have successfully claimed ' . $reward->title . '! Scan more QR Codes to earn more rewards.'
                ]);
                $merchantUser = MerchantUser::with('user')->where('merchant_id', $merchant->id)->first();
                Notification::create([
                    'merchant_id' => $data['merchant_id'],
                    'user_id' => $merchantUser->user->id,
                    'title' => 'Reward Claimed',
                    'message' => $user->first_name . ' ' . $user->last_name . ' have successfully claimed ' . $reward->title . '.'
                ]);

                return response()->json(['message' => 'Operation success'], 200);
            } else {
                return response()->json(['message' => 'You do not have enough points to claim this reward.'], 401);
            }
        });
    }

    public function getClaimedRewards() {
        $user = Auth::user();
        if($user->is_merchant) {
            $merchantId = Auth::user()->merchants()->first()->id;
            return response()->json(['claimed' => Claim::with(['reward', 'user'])->where('merchant_id', $merchantId)->get()]);
        }

        return response()->json(['claimed' => Claim::with('reward')->where('user_id', $user->id)->get()]);
    }
}
