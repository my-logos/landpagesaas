<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use App\Models\SupportTicket;
use App\Helpers\TranslationHelper;

class NewSupportTicketNotification extends Mailable
{
    use Queueable, SerializesModels;

    public $ticket;

    public function __construct(SupportTicket $ticket)
    {
        $this->ticket = $ticket;
    }

    public function build()
    {
        $locale = app()->getLocale();
        $subject = $locale === 'ar'
            ? 'تذكرة دعم جديدة #' . $this->ticket->ticket_number
            : 'New Support Ticket #' . $this->ticket->ticket_number;

        return $this->subject($subject)
            ->view('emails.new_support_ticket_notification');
    }
}
