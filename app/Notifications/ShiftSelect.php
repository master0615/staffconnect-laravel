<?php
namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class ShiftSelect extends Notification implements ShouldQueue
{
    use Queueable;

    private $greeting, $introLines = [], $actionButtons = [], $outroLines = [], $salutation, $data;

    public function __construct($data)
    {
        $this->data = $data;
        
        $this->greeting = "Hi!";
        $this->introLines = [
            "You have been selected for the role *Sampler* in shift *test shift* on *12 December*"
        ];
        $this->actionButtons = [
            [
                'actionText' => 'Confirm',
                'actionUrl' => '/',
                'actionColor' => 'green'
            ],
            [
                'actionText' => 'Replace',
                'actionUrl' => '/',
                'actionColor' => 'red'
            ]
        ];
        $this->outroLines = [
            "Please log in to confirm your availability and book new shifts."
        ];
        $this->salutation = "Thanks :) Team StaffConnect";
    }

    public function via($notifiable)
    {
        return [
            'mail'
        ];
    }

    public function toMail($notifiable)
    {
        //dd($this->actionButtons);
        return (new MailMessage())->markdown('emails.email2', [
            'greeting' => $this->greeting,
            'introLines' => $this->introLines,
            'actionButtons' => $this->actionButtons,
            'outroLines' => $this->outroLines,
            'salutation' => $this->salutation
        ]);
    }

    public function toArray($notifiable)
    {
        return [ //
];
    }
}
