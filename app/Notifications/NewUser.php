<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class NewUser extends Notification implements ShouldQueue
{
    use Queueable;

    private $greeting, $introLines = [], $actionButtons = [], $outroLines = [], $salutation;

    public function __construct($password)
    {

        $this->greeting = "Welcome!";
        $this->introLines = [
            "An account has been created for you on StaffConnect password $password",
        ];
        $this->actionButtons = [
            [
                'actionText' => 'Login',
                'actionUrl' => '/',
                'actionColor' => 'green',
            ],
        ];
        $this->outroLines = [
            "Please log in to update your profile.",
        ];
        $this->salutation = "Thanks :) Team StaffConnect";
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed   $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed                                            $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        return (new MailMessage())->markdown('emails.email2', [
            'greeting' => $this->greeting,
            'introLines' => $this->introLines,
            'actionButtons' => $this->actionButtons,
            'outroLines' => $this->outroLines,
            'salutation' => $this->salutation,
        ]);
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed   $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        return [
            //
        ];
    }
}
