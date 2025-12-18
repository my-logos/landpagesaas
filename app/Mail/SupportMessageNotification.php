<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use App\Models\Message;
use App\Models\Order;

class SupportMessageNotification extends Mailable
{
    use Queueable, SerializesModels;

    public $message;
    public $order;

    public function __construct(Message $message, Order $order)
    {
        $this->message = $message;
        $this->order = $order;
    }

    public function build()
    {
        $locale = app()->getLocale();
        $subject = $locale === 'ar'
            ? 'رسالة دعم جديدة - ' . config('app.name')
            : 'New Support Message - ' . config('app.name');

        return $this->subject($subject)
            ->view('emails.support_message_notification');
    }
}
