<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Srmklive\PayPal\Services\PayPal as PayPalClient;

class PaymentController extends Controller
{
    public function create(Request $request, $mode) {
        $type = $request->query('type', 'monthly');
        $amount = 0;
        switch ($mode) {
            case 'starter':
                $amount = 15;
                break;
            case 'business':
                $amount = 50;
                break;
            case 'corporate':
                $amount = 150;
                break;
            default:
                return response('Invalid request.',401);
        }
        switch ($type) {
            case 'monthly':
                // no-op
                break;
            case 'annual':
                $amount = $amount * 12;
                $amount = round($amount * ((100-15) / 100), 2);
                break;
            default:
                return response('Invalid request.',401);
        }
        $merchantId = Auth::user()->merchants()->first()->id;
        $provider = new PayPalClient;
        $provider->setApiCredentials(config('paypal'));
        $paypalToken = $provider->getAccessToken();
        $response = $provider->createOrder([
            "intent" => "CAPTURE",
            "application_context" => [
                "return_url" => route('paypal-payment-success'),
            ],
            "purchase_units" => [
                0 => [
                    "amount" => [
                        "currency_code" => "USD",
                        "value" => $amount
                    ],
                    "description" => "Payment for ". $type ." subscription for ". ucfirst($mode) ." plan.",
                    "payee" => [
                        "email_address" => Auth::user()->email,
                    ],
                    "breakdown" => [
                        "item_total" => [
                            "currency_code" => "USD",
                            "value" => $amount,
                        ]
                    ]
                ]
            ],
            "items" => [
                0 => [
                    "name" => ucfirst($type) . " subscription for Kembaq's " . ucfirst($mode) . " plan.",
                    "unit_amount" => [
                        "currency_code" => "USD",
                        "value" => $amount,
                    ],
                    "quantity" => "1",
                ]
            ],
            "payer" => [
                "brand_name" => "Kembaq",
            ]
        ]);
        if (isset($response['id']) && $response['id'] != null) {
            foreach ($response['links'] as $links) {
                if ($links['rel'] == 'approve') {
                    return response($links['href'], 200);
                }
            }

            return response('Something went wrong.', 500);
        } else {
            return response('Something went wrong.', 500);
        }
    }

    public function success(Request $request) {
        Log::debug($request);
    }
}
