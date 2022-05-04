<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;


class PointsController extends Controller
{
    public function getBalance(Request $request) {
        $wallet = $request->get('user_wallet');

        return response($wallet->balance);
    }

    public function depositBalance(Request $request) {
        request()->validate([
            'value' => 'required|numeric|min:1|max:100',
        ]);

        $wallet = $request->get('user_wallet');
        $merchantWallet = $request->get('merchant_wallet');

        $merchantWallet->transfer($wallet, $request->value);

        return response($wallet->balance);
    }

  
}
