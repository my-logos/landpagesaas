<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use App\Models\Message;
use App\Models\User;

class NewCustomerMessage extends Mailable
{
    use Queueable, SerializesModels;

    public $message;
    public $user;

    public function __construct(Message $message, User $user)
    {
        $this->message = $message;
        $this->user = $user;
    }

    public function build()
    {
        $locale = app()->getLocale();
        $subject = $locale === 'ar'
            ? 'رسالة جديدة من عميل - ' . config('app.name')
            : 'New Customer Message - ' . config('app.name');

        return $this->subject($subject)
            ->view('emails.new_customer_message');
    }
}
