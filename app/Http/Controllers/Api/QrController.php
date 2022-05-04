<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Carbon\Carbon;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class QrController extends Controller
{
    public function generateQrCodeUser(Request $request) {
        // request()->validate([
        //     'user_id' => 'required|exists:users,id',
        // ]);

        $validity = Carbon::now('UTC')->addMinutes(30);
        $user = User::findOrFail(1);
        $message = 'id:'.'1'.';name:'.$user->name.';expires_at:'.$validity->getTimestampMs();
        $checksum = crc32($message);
        $message = $message.';checksum:'.$checksum;
        
        return response(QrCode::format('svg')->size(800)->format('png')->style('round')->errorCorrection('L')->generate($message));
    }

    public function generateQrCodeMerchant(Request $request) {
        request()->validate([
            'merchant_id' => 'required|exists:merchants,id',
        ]);

        $validity = Carbon::now('UTC')->addSeconds(180);
        $message = 'id:'.$request->merchant_id.';expires_at:'.$validity->getTimestampMs();
        $checksum = crc32($message);
        $message = $message.';checksum:'.$checksum;
        
        return response()->json([
            'data' => QrCode::size(800)->format('png')->style('dot')->eye('circle')->errorCorrection('H')->merge($request->get('merchant')->logo, .3, true)->generate($message),
        ]);
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
        
        if($currentTimeUTC->greaterThanOrEqualTo($expiryDate)) {
            return response()->json([
                'message' => 'Code already expired.'
            ], 400);
        }

        return response()->json([
            'OK.'
        ], 200);
    }

    public function validateQrCodeMerchant(Request $request) {
        request()->validate([
            'id' => 'required|exists:merchants,id',
            'expires_at' => 'required|numeric',
            'checksum' => 'required|numeric',
        ]);

        $reconstructed = 'id:'.$request->id.';expires_at:'.$request->expires_at;
        $reconstructedChecksum = crc32($reconstructed);
        if($request->checksum != $reconstructedChecksum) {
            return response('Invalid checksum.', 400);
        }

        $currentTimeUTC = Carbon::now('UTC');
        $expiryDate = Carbon::createFromTimestampMsUTC($request->expires_at);
        
        if($currentTimeUTC->greaterThanOrEqualTo($expiryDate)) {
            return response('Code already expired.', 400);
        }

        return response('OK.', 200);
    }
}
