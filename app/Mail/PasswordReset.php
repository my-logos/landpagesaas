<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use App\Models\User;
use Illuminate\Support\Facades\URL;

class PasswordReset extends Mailable
{
    use Queueable, SerializesModels;

    public $user;
    public $resetUrl;
    public $token;

    public function __construct(User $user, string $token)
    {
        $this->user = $user;
        $this->token = $token;
        // Generate password reset URL
        $this->resetUrl = url('/password/reset/' . $token . '?email=' . urlencode($user->email));
    }

    public function build()
    {
        return $this->subject('Password Reset Request - ' . config('app.name'))
            ->view('emails.password_reset');
    }
}
