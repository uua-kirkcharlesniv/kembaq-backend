<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Ledger;
use App\Models\Merchant;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LedgerController extends Controller
{
    public function getTransactionsMerchant() {
        if(Auth::user()->is_merchant != 1) {
            return response('Unauthorized', 401);
        }

        return response()->json(['transactions' => Ledger::with('user')->where('merchant_id', Auth::user()->merchant_id)->get()]);
    }

    public function getTransactionsUser(Request $request) {
        if(Auth::user()->is_merchant != 0) {
            return response('Unauthorized', 401);
        }

        return response()->json(['transactions' => Ledger::with('merchant')->where('user_id', Auth::user()->id)->get()]);
    }

    public function getTransactionsUserMerchant(Request $request) {
        $request->validate([
            'merchant_id' => 'required|exists:merchants,id'
        ]);

        return response()->json(['transactions' => Ledger::where(['user_id' => Auth::user()->id], ['merchant_id' => $request->merchant_id])->get()]);
    }
}
