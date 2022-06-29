<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Message;
use App\Models\Notification;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    public function getAllNotifications() {
        return response()->json(['notifications' => Notification::with('merchant')->where('user_id', Auth::user()->id)->get()]);
    }
    
    public function getAllMessages() {
        $subscriptions = User::with('subscriptions')->findOrFail(Auth::user()->id)['subscriptions'];
        $subscribedIds = collect($subscriptions)->map(function ($subscription) {
            return $subscription->merchant_id;
        });

        return response()->json(['messages' => Message::with('merchant')->whereIn('merchant_id', $subscribedIds)->get()]);
    }
}
