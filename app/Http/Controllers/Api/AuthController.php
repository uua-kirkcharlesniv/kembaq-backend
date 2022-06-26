<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Merchant;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class AuthController extends Controller
{
    public function register(Request $request): JsonResponse
    {
        $request->validate([
            'email' => 'email|required|unique:users',
            'last_name' => 'required|max:55',
            'first_name' => 'required|max:55',
            'password' => 'required',
            'is_merchant' => 'nullable|boolean',
        ]);
        $data = [
            'last_name' => $request->input('last_name'),
            'first_name' => $request->input('first_name'),
            'email' => $request->input('email'),
            'password' => Hash::make($request->input('password')),
        ];

        if($request->has('is_merchant')) {
            $data = array_merge($data, ['is_merchant' => $request->input('is_merchant')]);
        }

        $user = User::create($data);

        $accessToken = $user->createToken('moveupapp')->accessToken;

        return response()->json(['user' => $user, 'access_token' => $accessToken]);
    }

    public function getCategories()
    {
        return response()->json(['categories' => Category::all()]);
    }

    public function createMerchantPage(Request $request)
    {
        $request->validate([
            'phone' => 'required',
            'logo' => 'required|file',
            'background_color' => [
                'required',
                'regex:/^#([A-Fa-f0-9]{6}|[A-Fa-f0-9]{3})$/'
            ],
            'button_color' => [
                'required',
                'regex:/^#([A-Fa-f0-9]{6}|[A-Fa-f0-9]{3})$/'
            ],
            'business_address' => 'required|string',
            'lat' => 'required',
            'long' => 'required',
            'business_name' => 'required',
            'category' => [
                'required',
                'exists:categories,id'
            ],
            'loyalty_type' => 'required|numeric|in:0,1',
            'currency' => 'required|string',
            'loyalty_value' => 'required|numeric|min:1|max:16777215'
        ]);

        $mc = Merchant::create([
            'mobile_number' => $request->input('phone'),
            'logo' => 'http://assets.stickpng.com/images/62306f7fa39b9e9c515e5925.png',
            'background_color' => $request->input('background_color'),
            'button_color' => $request->input('button_color'),
            'business_address' => $request->input('business_address'),
            'lat' => floatval($request->input('lat')),
            'long' => floatval($request->input('lat')),
            'business_name' => $request->input('business_name'),
            'category' => $request->input('category'),
            'loyalty_type' => $request->input('loyalty_type'),
            'currency' => $request->input('currency'),
            'loyalty_value' => $request->input('loyalty_value'),
        ]);

        DB::table('merchant_user')->insert([
            ['merchant_id' => $mc->id, 'user_id' => auth()->user()->id, 'role' => 'admin']
        ]);

        return response('Resource created', 200);
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'email|required',
            'password' => 'required',
            'notification_token' => 'nullable|string',
            'is_merchant' => 'nullable|boolean',
        ]);

        if (!(Auth::attempt(['email' => $request->input('email'), 'password' => $request->input('password')]))) {
            return response()->json(['data' => 'Invalid Credentials'], 422);
        }

        $user = User::with('merchants', 'merchants.category')->findOrFail(Auth::user()->id);

        if($request->has('is_merchant')) {
            if(($request->boolean('is_merchant') == true && $user->is_merchant == 0) || $request->boolean('is_merchant') == false && $user->is_merchant == 1) {
                return response('Unauthorized access', 401);
            }
        }

        $accessToken = $user->createToken('moveupapp')->accessToken;

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
