<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use App\Models\Message;
use App\Models\MessageReply;
use App\Models\User;

class NewCustomerReply extends Mailable
{
    use Queueable, SerializesModels;

    public $message;
    public $reply;
    public $user;

    public function __construct(Message $message, MessageReply $reply, User $user)
    {
        $this->message = $message;
        $this->reply = $reply;
        $this->user = $user;
    }

    public function build()
    {
        $locale = app()->getLocale();
        $subject = $locale === 'ar'
            ? 'رد جديد من عميل - ' . config('app.name')
            : 'New Customer Reply - ' . config('app.name');

        return $this->subject($subject)
            ->view('emails.new_customer_reply');
    }
}
