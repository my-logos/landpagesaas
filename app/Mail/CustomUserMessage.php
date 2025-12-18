<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use App\Models\User;

class CustomUserMessage extends Mailable
{
    use Queueable, SerializesModels;

    public $user;
    public $emailSubject;
    public $emailMessage;

    public function __construct(User $user, string $subject, string $message)
    {
        $this->user = $user;
        $this->emailSubject = $subject;
        $this->emailMessage = $message;
    }

    public function build()
    {
        return $this->subject($this->emailSubject)
            ->view('emails.custom_user_message')
            ->with([
                'emailSubject' => $this->emailSubject,
                'emailMessage' => $this->emailMessage
            ]);
    }
}
