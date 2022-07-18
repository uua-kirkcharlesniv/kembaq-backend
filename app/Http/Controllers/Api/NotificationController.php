<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Merchant;
use App\Models\Message;
use App\Models\Notification;
use App\Models\Subscription;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Laravel\Ui\Presets\React;

class NotificationController extends Controller
{
    public function getAllNotifications() {
        return response()->json(['notifications' => Notification::with('merchant')->where('user_id', Auth::user()->id)->get()]);
    }
    
    public function getAllMessages() {
        if(Auth::user()->is_merchant && Auth::user()->is_merchant_profile_created) {
            return response()->json(['messages' => Message::where('merchant_id', '=', Auth::user()->merchants()->first()->id)->get()]);
        }
        $subscriptions = User::with('subscriptions')->findOrFail(Auth::user()->id)['subscriptions'];
        $subscribedIds = collect($subscriptions)->map(function ($subscription) {
            return $subscription->merchant_id;
        });

        return response()->json(['messages' => Message::with('merchant')->whereIn('merchant_id', $subscribedIds)->get()]);
    }

    public function createNotification(Request $request) {
        Notification::create([
            'user_id' => 1,
            'merchant_id' => 1,
            'title' => 'Test',
            'message' => 'Message',
        ]);
    }
}
