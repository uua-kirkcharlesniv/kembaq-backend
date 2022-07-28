<?php

namespace App\Listeners;

use App\Models\Subscription;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;

class MessageListener
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  object  $event
     * @return void
     */
    public function handle($event)
    {
        $message = $event->message;
        if($message->type == 0) return;
        $subscriptions = Subscription::with('user')->where('merchant_id', $message->merchant_id)->get();
        $ids = [];
        foreach ($subscriptions as $key => $subscription) {
            if($subscription->user->notification_token != null) {
                array_push($ids, $subscription->user->notification_token);
            }
        }
        if(!empty($ids)) {
            $endpoint = "https://fcm.googleapis.com/fcm/send";
            $client = new \GuzzleHttp\Client();
            
            $response = $client->request('POST', $endpoint, [
                'json' => [
                    "registration_ids" => array_values($ids),
                    "notification" => [
                        "title" => $message->title,
                        "body" => $message->message,
                    ]
                ],
                'headers' => [
                    'Authorization' => 'key=AAAAsL5Tuxk:APA91bHkEUwj4ZeRekvhou3qurE7EMEAJapQVmooh7stFjqbzYuwcNLa0u9qnABUCwz6XRHGIXJMbPv2HWO3TiwkiQln29Rji24pKehyrF3-pPmOqVJbSY0ucad3FSC0KXI4oud1b6iU',
                    'Content-Type' => 'application/json'
                ]
            ]);

            Log::debug($response->getBody());
        }
        // Log::debug($subscriptions);
    }
}
