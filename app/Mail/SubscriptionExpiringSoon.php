<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use App\Models\User;
use App\Models\Subscription;
use App\Models\SubscriptionPackage;

class SubscriptionExpiringSoon extends Mailable
{
    use Queueable, SerializesModels;

    public $user;
    public $subscription;
    public $package;
    public $daysRemaining;

    public function __construct(User $user, Subscription $subscription, SubscriptionPackage $package, int $daysRemaining)
    {
        $this->user = $user;
        $this->subscription = $subscription;
        $this->package = $package;
        $this->daysRemaining = $daysRemaining;
    }

    public function build()
    {
        return $this->subject('Alert: Your Subscription Expires in ' . $this->daysRemaining . ' Days - ' . config('app.name'))
            ->view('emails.subscription_expiring_soon');
    }
}
