<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class TicketResolvedNotification extends Notification
{
    use Queueable;

    protected $ticket;
    protected $replyMessage;

    public function __construct($ticket, $replyMessage)
    {
        $this->ticket = $ticket;
        $this->replyMessage = $replyMessage;
    }

    public function via($notifiable)
    {
        return ['mail'];
    }

    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject('Your Ticket Has Been Resolved')
            ->line('Your ticket "' . $this->ticket->subject . '" has been resolved.')
            ->line('Admin Reply: ' . $this->replyMessage)
            ->action('View Ticket', url('/tickets/' . $this->ticket->id))
            ->line('Thank you for using our support system!');
    }
}
