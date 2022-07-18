<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\MerchantUser;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class EmployeeController extends Controller
{
    public function getEmployees(Request $request)
    {
        if(!Auth::user()->is_merchant || !Auth::user()->is_merchant_profile_created) {
            return response('You are not authorized or permitted to do this action', 401);
        }

        $employees = MerchantUser::with('user')->where('merchant_id', Auth::user()->merchants()->first()->id)->where('role', 'employee')->get();

        return response()->json(['employees' => $employees]);
    }

    public function createEmployee(Request $request)
    {
        if(!Auth::user()->is_merchant || !Auth::user()->is_merchant_profile_created) {
            return response('You are not authorized or permitted to do this action', 401);
        }
        
        $merchantId = Auth::user()->merchants()->first()->id;

        $request->validate([
            'email' => 'email|required|unique:users',
            'last_name' => 'required|max:55',
            'first_name' => 'required|max:55',
            'password' => 'required',
            'phone' => 'required|string',
        ]);

        $data = [
            'last_name' => $request->input('last_name'),
            'first_name' => $request->input('first_name'),
            'email' => $request->input('email'),
            'password' => Hash::make($request->input('password')),
            'is_merchant' => true,
            'phone' => $request->input('phone')
        ];

        $user = User::create($data);

        DB::table('merchant_user')->insert([
            ['merchant_id' => $merchantId, 'user_id' => $user->id, 'role' => 'employee']
        ]);

        $accessToken = $user->createToken('moveupapp')->accessToken;

        return response()->json(['user' => $user, 'access_token' => $accessToken]);
    }

    public function updateEmployee(Request $request, $id)
    {
        if(!Auth::user()->is_merchant || !Auth::user()->is_merchant_profile_created) {
            return response('You are not authorized or permitted to do this action', 401);
        }
        
        $data = $request->validate([
            'email' => 'email|nullable|unique:users',
            'last_name' => 'nullable|max:55',
            'first_name' => 'nullable|max:55',
            'password' => 'nullable|string',
            'phone' => 'nullable|string',
        ]);

        User::where('id', $id)->update(array_filter($data));
        $user = User::findOrFail($id);
        $accessToken = $user->createToken('moveupapp')->accessToken;

        return response()->json(['user' => $user, 'access_token' => $accessToken]);
    }

    public function deleteEmployee(Request $request, $id)
    {
        User::findOrFail($id)->delete();
        
        MerchantUser::where('merchant_id', Auth::user()->merchants()->first()->id)->where('user_id', $id)->delete();

        return response('Resource deleted', 200);
    }
}
