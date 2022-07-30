<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Ledger;
use App\Models\MerchantUser;
use App\Models\Notification;
use App\Models\Subscription;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PointsController extends Controller
{
    public function getBalance(Request $request) {
        $subscription = $request->get('subscription');

        return response($subscription->balance);
    }

    public function depositBalance(Request $request) {
        request()->validate([
            'value' => 'required|numeric|min:1',
            'sender_id' => 'nullable|exists:users,id'
        ]);
        
        $value = $request->value;
        $subscription = $request->get('subscription');

        DB::transaction(function () use ($subscription, $value, $request) {
            DB::table('subscriptions')->where('id', $subscription->id)->increment('balance', $value);
            $subscription->refresh();
            Ledger::create([
                'subscription_id' => $subscription->id,
                'merchant_id' => $subscription->merchant_id,
                'user_id' => $subscription->user_id,
                'value' => $value,
                'running_balance' => $subscription->balance,
                'sender_id' => $request->sender_id,
            ]);
            Notification::create([
                'merchant_id' => $subscription->merchant_id,
                'user_id' => $subscription->user_id,
                'title' => $request->get('merchant')->loyalty_type == 0 ? 'Stamps earned' : 'Points earned',
                'message' => 'You have received ' . $value . ($request->get('merchant')->loyalty_type == 0 ? ' stamp(s) ' : ' point(s) ') . 'from ' . $request->get('merchant')->business_name . '. Your current running balance is ' . $subscription->balance . '.',
            ]);
            $merchantUser = MerchantUser::with('user')->where('merchant_id', $subscription->merchant_id)->first();
            if($request->filled('sender_id') && $merchantUser->user->id != $request->sender_id) {
                Notification::create([
                    'merchant_id' => $subscription->merchant_id,
                    'user_id' => $request->sender_id,
                    'title' => $request->get('merchant')->loyalty_type == 0 ? 'Stamps given' : 'Points given',
                    'message' => 'You have sent ' . $value . ($request->get('merchant')->loyalty_type == 0 ? ' stamp(s) ' : ' point(s) ') . 'to ' . $subscription->user->first_name . ' ' . $subscription->user->last_name . '.',
                ]);
            }
            Notification::create([
                'merchant_id' => $subscription->merchant_id,
                'user_id' => $merchantUser->user->id,
                'title' => $request->get('merchant')->loyalty_type == 0 ? 'Stamps given' : 'Points given',
                'message' => 'You have sent ' . $value . ($request->get('merchant')->loyalty_type == 0 ? ' stamp(s) ' : ' point(s) ') . 'to ' . $subscription->user->first_name . ' ' . $subscription->user->last_name . '.',
            ]);
        });

        return response($subscription->balance);
    }
}
