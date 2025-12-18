<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use App\Models\Order;
use App\Models\User;

class NewOrderNotification extends Mailable
{
    use Queueable, SerializesModels;

    public $order;
    public $user;

    public function __construct(Order $order, User $user)
    {
        $this->order = $order;
        $this->user = $user;
    }

    public function build()
    {
        $locale = app()->getLocale();
        $subject = $locale === 'ar'
            ? 'طلب جديد - ' . config('app.name')
            : 'New Order - ' . config('app.name');

        return $this->subject($subject)
            ->view('emails.new_order_notification');
    }
}
