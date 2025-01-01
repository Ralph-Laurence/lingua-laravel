<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class RegistrationApprovedMail extends Mailable
{
    use Queueable, SerializesModels;

    public $emailData;
    /**
     * Create a new message instance.
     */
    public function __construct($emailData)
    {
        $this->emailData = $emailData;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Registration Approved',
        );
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        // $this->emailData['logo'] = $this->embed($this->emailData['logo']);

        return $this->subject('Registration Approved')
                ->view('mails.registration-approved')
                ->with('emailData', $this->emailData);
    }
}
