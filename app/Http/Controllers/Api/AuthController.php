<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Merchant;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function register(Request $request): JsonResponse
    {
        $request->validate([
            'email' => 'email|required|unique:users',
            'last_name' => 'required|max:55',
            'first_name' => 'required|max:55',
            'password' => 'required',
            'notification_token' => 'nullable',
        ]);

        $user = User::create([
            'last_name' => $request->input('last_name'),
            'first_name' => $request->input('first_name'),
            'email' => $request->input('email'),
            'password' => Hash::make($request->input('password')),
            // 'notification_token' => $request->input('notification_token'),
        ]);
        
        // TODO: Remove this fix
        $mc = Merchant::findOrFail(1);
        $user->createWallet([
            'name' => $user->last_name . ' ' . $user->first_name . ' - ' . $mc->business_name,
            'slug' => $user->id.'-'.$mc->id,
        ]);

        $accessToken = $user->createToken('harbinAuthToken')->accessToken;

        // TODO(uua-kirkcharlesniv): Verify if user is verified, if not, send an email confirmation
        //        if(!$user->is_verified) {
        //            $user->sendVerificationReminder();
        //        }

        return response()->json(['user' => $user, 'access_token' => $accessToken]);
    }

    public function login(Request $request): JsonResponse
    {
        $request->validate([
            'email' => 'email|required',
            'password' => 'required',
            'notification_token' => 'nullable|string',
        ]);

        if (!(Auth::attempt(['email' => $request->input('email'), 'password' => $request->input('password')]))) {
            return response()->json(['data' => 'Invalid Credentials'], 422);
        }

        $user = User::with('merchants', 'merchants.category')->findOrFail(Auth::user()->id);
        $accessToken = $user->createToken('harbinAuthToken')->accessToken;

        // TODO(uua-kirkcharlesniv): Verify if user is verified, if not, send an email confirmation
        //        if(!$user->is_verified) {
        //            $user->sendVerificationReminder();
        //        }

        return response()->json(['user' => $user, 'access_token' => $accessToken]);
    }

    public function changePassword(Request $request): JsonResponse
    {
        $request->validate([
            'old_password' => 'required',
            'new_password' => 'required|min:6',
            'confirm_password' => 'required|same:new_password'
        ]);

        if (!Hash::check($request->input('old_password'), Auth::user()->password)) {
            return response()->json([
                'message' => 'Invalid old password.'
            ], 400);
        }

        if (Hash::check($request->input('new_password'), Auth::user()->password)) {
            return response()->json([
                'message' => 'Your new password must not be the same as your old password.'
            ], 400);
        }

        $user = Auth::user();
        $user->update([
            'password' => Hash::make($request->input('new_password'))
        ]);

        return response()->json([
            'message' => 'Successfully changed your password.'
        ]);
    }
}
