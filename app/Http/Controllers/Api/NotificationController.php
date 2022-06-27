<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    public function getAllNotifications() {
        return response()->json(['notifications' => Notification::where('user_id', Auth::user()->id)->get()]);
    }
}
