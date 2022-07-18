<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Merchant;
use App\Models\User;
use Illuminate\Http\Request;
use Carbon\Carbon;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class QrController extends Controller
{
    public function generateRewardQr(Request $request) {
        if(!$request->headers->has('user_id')) {
            return response()->json([
                'message' => 'User ID is required.'
            ], 400);
        }

        if(!$request->headers->has('merchant_id')) {
            return response()->json([
                'message' => 'Merchant ID is required.'
            ], 400);
        }

        if(!$request->headers->has('reward_id')) {
            return response()->json([
                'message' => 'Reward ID is required.'
            ], 400);
        }

        $message = 'reward_id:'.$request->header('reward_id').
                    ';user_id:'.$request->header('user_id').
                    ';merchant_id:'.$request->header('merchant_id');
        $checksum = crc32($message);
        $message = $message.';checksum:'.$checksum;
        
        return response(QrCode::format('svg')->size(800)->format('png')->style('round')->errorCorrection('L')->generate($message));
    }

    public function generateQrCodeUser(Request $request) {
        if(!$request->headers->has('user_id')) {
            return response()->json([
                'message' => 'User ID is required.'
            ], 400);
        }

        $validity = Carbon::now('UTC')->addSeconds(180);
        $user = User::findOrFail($request->header('user_id'));
        $message = 'id:'.$request->header('user_id').';name:'.$user->name.';expires_at:'.$validity->getTimestampMs();
        $checksum = crc32($message);
        $message = $message.';checksum:'.$checksum;
        
        return response(QrCode::format('svg')->size(800)->format('png')->style('round')->errorCorrection('L')->generate($message));
    }

    public function generateQrCodeMerchant(Request $request) {
        if(!$request->headers->has('user_id')) {
            return response()->json([
                'message' => 'User ID is required.'
            ], 400);
        }

        if(!$request->headers->has('value')) {
            return response()->json([
                'message' => 'Value is required.'
            ], 400);
        }

        $validity = Carbon::now('UTC')->addSeconds(180);
        $merchant = Merchant::findOrFail($request->header('user_id'));
        $message = 'id:'.$merchant->id.';expires_at:'.$validity->getTimestampMs();
        $message .= ';value:'.$request->header('value');
        $checksum = crc32($message);
        $message .= ';checksum:'.$checksum;
        
        return response(QrCode::size(800)->format('png')->errorCorrection('M')->generate($message));
    }

    public function validateQrCodeUser(Request $request) {
        request()->validate([
            'id' => 'required|exists:users,id',
            'name' => 'required',
            'expires_at' => 'required|numeric',
            'checksum' => 'required|numeric',
        ]);

        $reconstructed = 'id:'.$request->id.';name:'.$request->name.';expires_at:'.$request->expires_at;
        $reconstructedChecksum = crc32($reconstructed);
        if($request->checksum != $reconstructedChecksum) {
            return response()->json([
                'message' => 'Cannot validate the authenticity of the QR code.'
            ], 400);
        }

        $currentTimeUTC = Carbon::now('UTC');
        $expiryDate = Carbon::createFromTimestampMsUTC($request->expires_at);
        
        // if($currentTimeUTC->greaterThanOrEqualTo($expiryDate)) {
        //     return response()->json([
        //         'message' => 'Code already expired.'
        //     ], 400);
        // }

        return response()->json([
            'OK.'
        ], 200);
    }

    public function validateQrCodeMerchant(Request $request) {
        request()->validate([
            'id' => 'required|exists:merchants,id',
            'expires_at' => 'required|numeric',
            'value' => 'required|numeric',
            'checksum' => 'required|numeric',
        ]);

        $reconstructed = 'id:'.$request->id.';expires_at:'.$request->expires_at.';value:'.$request->value;
        $reconstructedChecksum = crc32($reconstructed);
        if($request->checksum != $reconstructedChecksum) {
            return response()->json([
                'message' => 'Cannot validate the authenticity of the QR code.'
            ], 400);
        }

        $currentTimeUTC = Carbon::now('UTC');
        $expiryDate = Carbon::createFromTimestampMsUTC($request->expires_at);
        
        // if($currentTimeUTC->greaterThanOrEqualTo($expiryDate)) {
        //     return response()->json([
        //         'message' => 'Code already expired.'
        //     ], 400);
        // }

        return response()->json([
            'OK.'
        ], 200);
    }
}
