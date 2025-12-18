<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use App\Models\Order;

class OrderSuccessNotification extends Mailable
{
    use Queueable, SerializesModels;

    public $order;

    public function __construct(Order $order)
    {
        $this->order = $order;
    }

    public function build()
    {
        $locale = app()->getLocale();
        $subject = $locale === 'ar'
            ? 'تم استلام طلبك بنجاح - ' . config('app.name')
            : 'Your Order Has Been Received - ' . config('app.name');

        return $this->subject($subject)
            ->view('emails.order_success_notification');
    }
}
