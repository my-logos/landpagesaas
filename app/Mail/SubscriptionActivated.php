<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use App\Models\User;
use App\Models\Subscription;
use App\Models\SubscriptionPackage;

class SubscriptionActivated extends Mailable
{
    use Queueable, SerializesModels;

    public $user;
    public $subscription;
    public $package;

    public function __construct(User $user, Subscription $subscription, SubscriptionPackage $package)
    {
        $this->user = $user;
        $this->subscription = $subscription;
        $this->package = $package;
    }

    public function build()
    {
        return $this->subject('Your Subscription Has Been Activated - ' . config('app.name'))
            ->view('emails.subscription_activated');
    }
}
