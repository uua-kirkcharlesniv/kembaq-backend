<?php

namespace App\Listeners;

use App\Models\User;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;

class NotificationListener
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
        $notification = $event->notification;
        $user = User::findOrFail($notification->user_id);

        if($user->notification_token == null) {
            return;
        }

        $endpoint = "https://fcm.googleapis.com/fcm/send";
        $client = new \GuzzleHttp\Client();
        
        $client->request('POST', $endpoint, [
            'json' => [
                "to" => $user->notification_token,
                "notification" => [
                    "title" => $notification->title,
                    "body" => $notification->message,
                ]
            ],
            'headers' => [
                'Authorization' => 'key=AAAAsL5Tuxk:APA91bHkEUwj4ZeRekvhou3qurE7EMEAJapQVmooh7stFjqbzYuwcNLa0u9qnABUCwz6XRHGIXJMbPv2HWO3TiwkiQln29Rji24pKehyrF3-pPmOqVJbSY0ucad3FSC0KXI4oud1b6iU',
                'Content-Type' => 'application/json'
            ]
        ]);
    }
}
