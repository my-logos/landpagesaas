<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use App\Models\SupportTicket;
use App\Models\SupportTicketReply;
use App\Helpers\TranslationHelper;

class SupportTicketReplyNotification extends Mailable
{
    use Queueable, SerializesModels;

    public $ticket;
    public $reply;
    public $isAdminReply;

    public function __construct(SupportTicket $ticket, SupportTicketReply $reply, bool $isAdminReply = false)
    {
        $this->ticket = $ticket;
        $this->reply = $reply;
        $this->isAdminReply = $isAdminReply;
    }

    public function build()
    {
        $locale = app()->getLocale();

        if ($this->isAdminReply) {
            // Admin replied to user
            $subject = $locale === 'ar'
                ? 'رد على تذكرتك #' . $this->ticket->ticket_number
                : 'Reply to Your Ticket #' . $this->ticket->ticket_number;
            $view = 'emails.support_ticket_admin_reply';
        } else {
            // User replied to admin
            $subject = $locale === 'ar'
                ? 'رد جديد على التذكرة #' . $this->ticket->ticket_number
                : 'New Reply on Ticket #' . $this->ticket->ticket_number;
            $view = 'emails.support_ticket_user_reply';
        }

        return $this->subject($subject)
            ->view($view);
    }
}
