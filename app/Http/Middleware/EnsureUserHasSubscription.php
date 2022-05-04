<?php

namespace App\Http\Middleware;

use App\Models\Merchant;
use App\Models\User;
use Closure;
use Illuminate\Http\Request;

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
        $walletSlug = $user->id.'-'.$request->merchant_id;

        if(!($user->hasWallet($walletSlug))) {
            return response('Wallet not found.', 404);
        }

        $wallet = $user->getWallet($walletSlug);

        $merchant = Merchant::findOrFail($request->merchant_id);
        $merchantWallet = $merchant->wallet;

        $request->attributes->add([
            'user_wallet' => $wallet,
            'merchant_wallet' => $merchantWallet,
            'user' => $user,
            'merchant' => $merchant,
        ]);

        return $next($request);
    }
}
