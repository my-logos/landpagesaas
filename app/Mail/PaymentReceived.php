<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use App\Models\Payment;
use App\Models\User;

class PaymentReceived extends Mailable
{
    use Queueable, SerializesModels;

    public $payment;
    public $user;

    public function __construct(Payment $payment, User $user)
    {
        $this->payment = $payment;
        $this->user = $user;
    }

    public function build()
    {
        return $this->subject('New Payment Received - ' . config('app.name'))
            ->view('emails.admin.payment_received');
    }
}
