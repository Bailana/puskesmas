<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Models\User;

class PasswordChangeConfirmation extends Mailable
{
    use Queueable, SerializesModels;

    public $user;
    public $token;

    /**
     * Create a new message instance.
     *
     * @param User $user
     * @param string $token
     */
    public function __construct(User $user, $token)
    {
        $this->user = $user;
        $this->token = $token;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $url = url('/resepsionis/profile/confirm-password-change/' . $this->token);

        return $this->subject('Confirm Your Password Change')
                    ->view('emails.password_change_confirmation')
                    ->with([
                        'name' => $this->user->name,
                        'url' => $url,
                    ]);
    }
}
