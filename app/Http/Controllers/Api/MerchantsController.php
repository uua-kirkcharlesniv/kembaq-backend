<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Merchant;
use App\Models\Message;
use App\Models\Reward;
use App\Models\Subscription;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class MerchantsController extends Controller
{
    public function getAllMerchants()
    {
        return response()->json(['merchants' => Merchant::all()]);
    }

    public function fetchAllSubscribedMerchants()
    {
        if (Auth::user()->is_merchant && Auth::user()->is_merchant_profile_created) {
            return response()->json(['subscribed' => Subscription::with('user')->where('merchant_id', Auth::user()->merchants()->first()->id)->get()]);
        }
        return response()->json(['subscribed' => User::with('subscriptions', 'subscriptions.merchant', 'subscriptions.merchant.rewards')->findOrFail(Auth::user()->id)['subscriptions']]);
    }

    public function fetchAllAvailableMerchants()
    {
        $subscriptions = User::with('subscriptions')->findOrFail(Auth::user()->id)['subscriptions'];
        $subscribedIds = collect($subscriptions)->map(function ($subscription) {
            return $subscription->merchant_id;
        });

        return response()->json(['available' =>  Merchant::whereNotIn('id', $subscribedIds)->get()]);
    }

    public function getMerchantMessages(Request $request, $id)
    {
        return response()->json(['messages' =>  Message::where('merchant_id', $id)->get()]);
    }

    public function getMerchantRewards(Request $request, $id)
    {
        $subscription = Subscription::where(['merchant_id' => $id, 'user_id' => Auth::user()->id])->first();
        $merchant = Merchant::findOrFail($id);

        return response()->json(['rewards' => Reward::with('merchant')->where('merchant_id', $id)->get(), 'subscription' => $subscription, 'merchant' => $merchant]);
    }

    public function getAllRewards()
    {
        if (Auth::user()->is_merchant && Auth::user()->is_merchant_profile_created) {
            return response()->json(['rewards' => Reward::where('merchant_id', '=', Auth::user()->merchants()->first()->id)->get()]);
        }
        
        $subscriptions = User::with('subscriptions')->findOrFail(Auth::user()->id)['subscriptions'];
        $subscribedIds = collect($subscriptions)->map(function ($subscription) {
            return $subscription->merchant_id;
        });

        return response()->json(['rewards' => Reward::with('merchant')->whereIn('merchant_id', $subscribedIds)->get()]);
    }

    public function getMerchant(Request $request, $id)
    {
        $user = User::findOrFail($id);
        $merchantId = Auth::user()->merchants()->first()->id;
        return response()->json(['merchant' => Merchant::with('rewards', 'messages')->findOrFail($merchantId)]);
    }

    public function createMessage(Request $request)
    {
        if (!Auth::user()->is_merchant || !Auth::user()->is_merchant_profile_created) {
            return response('You are not authorized or permitted to do this action', 401);
        }
        $request->validate([
            'title' => 'required',
            'link' => 'required',
            'message' => 'required',
            'photo' => 'required',
        ]);

        $filename = "messages/" . auth()->user()->id . "/" . Carbon::now()->format('YmdHms') . ".png";
        if (is_string($request->photo)) {
            $logoAsset = base64_decode(substr($request->photo, strpos($request->photo, ',') + 1));
            Storage::disk('public')->put($filename, $logoAsset);
        } else {
            $request->validate([
                'photo' => 'required|image',
            ]);
            Storage::disk('public')->put($filename, file_get_contents($request->file('photo')));
        }

        $data = Message::create([
            'merchant_id' => Auth::user()->merchants()->first()->id,
            'title' => $request->title,
            'link' => $request->link,
            'message' => $request->message,
            'photo' => $filename,
        ]);

        return response()->json([
            'data' => $data,
            'message' => 'Resource successfully created'
        ]);
    }

    public function createReward(Request $request)
    {
        if (!Auth::user()->is_merchant || !Auth::user()->is_merchant_profile_created) {
            return response('You are not authorized or permitted to do this action', 401);
        }

        $request->validate([
            'title' => 'required',
            'description' => 'required',
            'value' => 'required|numeric|min:1',
            'days' => 'required|numeric|min:1|max:365',
            'photo' => 'required'
        ]);

        $filename = "rewards/" . auth()->user()->id . "/" . Carbon::now()->format('YmdHms') . ".png";
        if (is_string($request->photo)) {
            $logoAsset = base64_decode(substr($request->photo, strpos($request->photo, ',') + 1));
            Storage::disk('public')->put($filename, $logoAsset);
        } else {
            $request->validate([
                'photo' => 'required|image',
            ]);
            Storage::disk('public')->put($filename, file_get_contents($request->file('photo')));
        }

        $data = Reward::create([
            'merchant_id' => Auth::user()->merchants()->first()->id,
            'title' => $request->title,
            'description' => $request->description,
            'value' => $request->value,
            'photo' => $filename,
            'valid_until' => Carbon::now()->utc()->addDays($request->days),
        ]);

        return response()->json([
            'data' => $data,
            'message' => 'Resource successfully created'
        ]);
    }

    public function deleteMessage($id)
    {
        Message::destroy($id);

        return response('Resource deleted', 200);
    }

    public function deleteReward($id)
    {
        Reward::destroy($id);

        return response('Resource deleted', 200);
    }

    public function updateMessage(Request $request, $id)
    {
        $data = $request->validate([
            'title' => 'nullable',
            'link' => 'nullable',
            'message' => 'nullable',
            'photo' => 'nullable'
        ]);

        $message = Message::findOrFail($id);

        if ($request->has('photo') && is_string($request->photo)) {
            $logoAsset = base64_decode(substr($request->photo, strpos($request->photo, ',') + 1));
            Storage::disk('public')->put($message->getRawOriginal('photo'), $logoAsset);
        } else if ($request->has('photo')) {
            $request->validate([
                'photo' => 'required|image',
            ]);
            Storage::disk('public')->put($message->getRawOriginal('photo'), file_get_contents($request->file('photo')));
        }
        unset($data['photo']);
        $message->update(array_filter($data));
        return response('Resource updated', 200);
    }

    public function updateReward(Request $request, $id)
    {
        $data = $request->validate([
            'title' => 'nullable',
            'description' => 'nullable',
            'value' => 'nullable|numeric|min:1',
            'days' => 'nullable|numeric|min:1|max:365',
            'photo' => 'nullable'
        ]);

        $reward = Reward::findOrFail($id);

        if ($request->has('photo') && is_string($request->photo)) {
            $logoAsset = base64_decode(substr($request->photo, strpos($request->photo, ',') + 1));
            Storage::disk('public')->put($reward->getRawOriginal('photo'), $logoAsset);
        } else if ($request->has('photo')) {
            $request->validate([
                'photo' => 'required|image',
            ]);
            Storage::disk('public')->put($reward->getRawOriginal('photo'), file_get_contents($request->file('photo')));
        }

        if($request->has('days')) {
            $data = array_merge($data, ['valid_until' => Carbon::now()->utc()->addDays($request->days)]);
        }
        unset($data['photo']);
        $reward->update(array_filter($data));
        return response('Resource updated', 200);
    }
}
