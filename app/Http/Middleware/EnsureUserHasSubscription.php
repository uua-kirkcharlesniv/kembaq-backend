<?php

namespace App\Http\Middleware;

use App\Models\Merchant;
use App\Models\Notification;
use App\Models\Subscription;
use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class EnsureUserHasSubscription
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        request()->validate([
            'user_id' => 'required|exists:users,id',
            'merchant_id' => 'required|exists:merchants,id',
        ]);

        $user = User::findOrFail($request->user_id);
        $merchant = Merchant::findOrFail($request->merchant_id);
        $subscription = Subscription::where('merchant_id', $request->merchant_id)->where('user_id', $request->user_id)->first();
        if(!$subscription) {
            $subscription = Subscription::create([
                'merchant_id' => $request->merchant_id,
                'user_id' => $request->user_id,
                'balance' => 0,
            ]);
            Notification::create([
                'merchant_id' => $request->merchant_id,
                'user_id' => $request->user_id,
                'title' => 'Welcome to ' . $merchant->business_name,
                'message' => 'Start earning rewards by purchasing items and scanning QR codes!'
            ]);
        }
        $request->attributes->add([
            'subscription' => $subscription,
            'user' => $user,
            'merchant' => $merchant,
        ]);

        return $next($request);
    }
}
