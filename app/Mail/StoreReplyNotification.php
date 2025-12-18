<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use App\Models\Message;
use App\Models\MessageReply;

class StoreReplyNotification extends Mailable
{
    use Queueable, SerializesModels;

    public $message;
    public $reply;

    public function __construct(Message $message, MessageReply $reply)
    {
        $this->message = $message;
        $this->reply = $reply;
    }

    public function build()
    {
        $locale = app()->getLocale();
        $subject = $locale === 'ar'
            ? 'رد من المتجر على رسالتك - ' . config('app.name')
            : 'Store Reply to Your Message - ' . config('app.name');

        return $this->subject($subject)
            ->view('emails.store_reply_notification');
    }
}
