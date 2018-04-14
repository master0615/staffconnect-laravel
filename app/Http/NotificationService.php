<?php

namespace App\Http;

use GuzzleHttp\Client;

class NotificationService
{
    /**
     * @param  string $token
     * @param  array
     * @return [type]
     */
    static public function pushFirebase($token, $sender, $payload = []) {
        try {            
            $headers = [
                'Content-Type' => 'application/json',
                // auth key using enviroment variable
                'Authorization' => 'key=' . env('FIREBASE_KEY'),
            ];
            
            // insert new broadcast
            $requestUrl = 'https://fcm.googleapis.com/fcm/send';

            $body = [
                'to' => $token,
                'data' => $payload,
                'notification' => [
                    'title' => $sender->fname . ' ' . $sender->lname,
                    'body' => $payload->content,
                    'icon' => $sender->tthumb()
                ]
            ];

            $client = new Client;
            
            $response = $client->request('POST', $requestUrl, [
                'headers' => $headers,
                'json' => $body,
                'verify' => false
            ]);
            
            $responseData = json_decode($response->getBody());
            
            \Log::info('send notification sucsess');
        }
        catch (\GuzzleHttp\Exception\ClientException $e){
            // handle request exception to firebase service here
            \Log::error('send notification failed' . $e->getMessage());          
        }
        catch (Exception $e) {
            \Log::error('send notification failed' . $e->getMessage());
        }
    }
}
