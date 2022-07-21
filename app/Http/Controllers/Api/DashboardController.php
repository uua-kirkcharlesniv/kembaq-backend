<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Ledger;
use App\Models\Subscription;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function getStats(Request $request)
    {
        if(!Auth::user()->is_merchant || !Auth::user()->is_merchant_profile_created) {
            return response('You are not authorized or permitted to do this action', 401);
        }
        $merchantId = Auth::user()->merchants()->first()->id;
        $month = Carbon::now()->month;
        $previousMonth = Carbon::now()->month - 1;
        if($month == 1) {
            $previousMonth = 12;
        }

        $data = Subscription::whereMonth('created_at', $month)->where('merchant_id', $merchantId)->count();
        $active = DB::table('ledgers')->whereRaw('MONTH(created_at) = '. $month)->where('merchant_id', '=', $merchantId)->count(DB::raw('DISTINCT user_id'));

        $previousData = Subscription::whereMonth('created_at', $previousMonth)->where('merchant_id', $merchantId)->count();
        $previousActive = DB::table('ledgers')->whereRaw('MONTH(created_at) = '. $previousMonth)->where('merchant_id', '=', $merchantId)->count(DB::raw('DISTINCT user_id'));

        return response()->json([
            'new' => $data,
            'active' => $active,
            'previousNew' => $previousData,
            'previousActive' => $previousActive,
            'currPrevNewChangePerc' => $this->getPercentageChange($previousData, $data),
            'currPrevActiveChangePerc' => $this->getPercentageChange($previousActive, $active),
        ]);
    }

    private function getPercentageChange($oldNumber, $newNumber){
        if($oldNumber == 0) {
            return "+".($newNumber * 100)."%";
        }

        $decreaseValue = $oldNumber - $newNumber;
    
        $value = ($decreaseValue / $oldNumber) * 100;

        if($oldNumber > $newNumber) {
            return "-".$value."%";
        } else {
            return "+".$value."%";
        }
    }
}
