<?php
namespace App\Services;

use App\Device;
use App\Message;
use App\User;
use App\Jobs\SendNotification;

/**
 *  @SWG\Tag(
 *      name="user",
 *      description="User"
 *  )
 */
class UserChatService
{

    public function __construct()
    {
    }

    public function sendNotification($body) {
      try {
        $sender = User::where('id', $body['sender_id'])->firstOrFail();
        $devices = Device::where('user_id', $body['receiver_id'])->get();
        $message = Message::where('id', $body['id'])->firstOrFail();

        /* Send FCM notification */
        $devices->each(function( $device) use ($sender, $message) {
            dispatch(new SendNotification($device->firebase_token, $sender, $message, true));
        });

        $message->sent = 1;
        $message->save();
      } catch (\Exception $e) {
        // @todo: Error handling
      }
    }
}
