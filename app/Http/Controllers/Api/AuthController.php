<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Merchant;
use App\Models\Notification;
use App\Models\Subscription;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

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
            'phone' => 'nullable|string',
            'notification_token' => 'nullable|string',
        ]);
        $data = [
            'last_name' => $request->input('last_name'),
            'first_name' => $request->input('first_name'),
            'email' => $request->input('email'),
            'password' => Hash::make($request->input('password')),
            'notification_token' => $request->input('notification_token'),
        ];

        if($request->has('is_merchant')) {
            $data = array_merge($data, ['is_merchant' => $request->input('is_merchant')]);
        }

        if($request->has('phone')) {
            $data = array_merge($data, ['phone' => $request->input('phone')]);
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
        $user = Auth::user();
        if($user->is_merchant != 1) {
            return response('Unauthorized access', 401);
        }

        if($user->is_merchant_profile_created) {
            return response('Merchant profile already exists.', 409);
        }

        $request->validate([
            'logo' => 'required',
            'hero' => 'required',
            'background_color' => [
                'required',
                'regex:/^#([A-Fa-f0-9]{6}|[A-Fa-f0-9]{3})$/'
            ],
            'button_color' => [
                'required',
                'regex:/^#([A-Fa-f0-9]{6}|[A-Fa-f0-9]{3})$/'
            ],
            'text_color' => [
                'required',
                'regex:/^#([A-Fa-f0-9]{6}|[A-Fa-f0-9]{3})$/'
            ],
            'border_color' => [
                'required',
                'regex:/^#([A-Fa-f0-9]{6}|[A-Fa-f0-9]{3})$/'
            ],
            'points_color' => [
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

        if(is_string($request->logo)) {
            $logoAsset = base64_decode(substr($request->logo, strpos($request->logo, ',') + 1));
            Storage::disk('public')->put("merchants/".auth()->user()->id."/logo.png", $logoAsset);
        } else {
            $request->validate([
                'logo' => 'required|image',
            ]);
            Storage::disk('public')->put("merchants/".auth()->user()->id."/logo.png", file_get_contents($request->file('logo')));
        }
        
        if(is_string($request->hero)) {
            $heroAsset = base64_decode(substr($request->hero, strpos($request->hero, ',') + 1));
            Storage::disk('public')->put("merchants/".auth()->user()->id."/hero.png", $heroAsset);
        } else {
            $request->validate([
                'hero' => 'required|image',
            ]);
            Storage::disk('public')->put("merchants/".auth()->user()->id."/hero.png", file_get_contents($request->file('hero')));
        }


        $mc = Merchant::create([
            'logo' => "merchants/".auth()->user()->id."/logo.png",
            'hero' => "merchants/".auth()->user()->id."/hero.png",
            'background_color' => $request->input('background_color'),
            'button_color' => $request->input('button_color'),
            'text_color' => $request->input('text_color'),
            'border_color' => $request->input('border_color'),
            'points_color' => $request->input('points_color'),
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

    public function updateMerchantPage(Request $request)
    {
        $user = Auth::user();
        if($user->is_merchant != 1) {
            return response('Unauthorized access', 401);
        }

        if(!$user->is_merchant_profile_created) {
            return response('Merchant profile does not exist yet.', 404);
        }

        $data = $request->validate([
            'logo' => 'nullable',
            'hero' => 'nullable',
            'background_color' => [
                'regex:/^#([A-Fa-f0-9]{6}|[A-Fa-f0-9]{3})$/'
            ],
            'button_color' => [
                'regex:/^#([A-Fa-f0-9]{6}|[A-Fa-f0-9]{3})$/'
            ],
            'text_color' => [
                'regex:/^#([A-Fa-f0-9]{6}|[A-Fa-f0-9]{3})$/'
            ],
            'border_color' => [
                'regex:/^#([A-Fa-f0-9]{6}|[A-Fa-f0-9]{3})$/'
            ],
            'points_color' => [
                'regex:/^#([A-Fa-f0-9]{6}|[A-Fa-f0-9]{3})$/'
            ],
            'business_address' => 'string',
            'lat' => 'string',
            'long' => 'string',
            'business_name' => 'string',
            'category' => [
                'exists:categories,id'
            ],
            'loyalty_type' => 'numeric|in:0,1',
            'currency' => 'string',
            'loyalty_value' => 'numeric|min:1|max:16777215'
        ]);

        $filename = "merchants/" . auth()->user()->id . '/logo/' . Carbon::now()->format('YmdHms') . ".png";
        if($request->has('logo') && is_string($request->logo)) {
            $logoAsset = base64_decode(substr($request->logo, strpos($request->logo, ',') + 1));
            Storage::disk('public')->put($filename, $logoAsset);
            unset($data['logo']);
            $data = array_merge($data, ["logo" => $filename]);
        } else if ($request->has('logo')) {
            $request->validate([
                'logo' => 'required|image',
            ]);
            Storage::disk('public')->put($filename, file_get_contents($request->file('logo')));
            unset($data['logo']);
            $data = array_merge($data, ["logo" => $filename]);
        }
        
        $heroFn = "merchants/" . auth()->user()->id . '/hero/' . Carbon::now()->format('YmdHms') . ".png";
        if($request->has('hero') && is_string($request->hero)) {
            $heroAsset = base64_decode(substr($request->hero, strpos($request->hero, ',') + 1));
            Storage::disk('public')->put($heroFn, $heroAsset);
            unset($data['hero']);
            $data = array_merge($data, ["hero" => $heroFn]);
        } else if ($request->has('hero')) {
            $request->validate([
                'hero' => 'required|image',
            ]);
            Storage::disk('public')->put($filename, file_get_contents($request->file('hero')));
            unset($data['hero']);
            $data = array_merge($data, ["hero" => $heroFn]);
        }

        Merchant::where('id', Auth::user()->merchants()->first()->id)->update($data);

        return response('Resource updated', 200);
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
        
        if($request->filled('notification_token')) {
            $user->update([
                'notification_token' => $request->notification_token
            ]);
        }

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

    public function updateProfile(Request $request)
    {
        $user = Auth::user();

        $data = $request->validate([
            'email' => 'email|nullable|unique:users,email,'.$user->id,
            'last_name' => 'nullable|max:55',
            'first_name' => 'nullable|max:55',
            'password' => 'nullable|string',
            'phone' => 'nullable|string',
        ]);

        User::where('id', $user->id)->update(array_filter($data));

        return response()->json([
            'message' => 'Successfully updated profile.',
            'data' => User::findOrFail($user->id),
        ]);
    }
}
