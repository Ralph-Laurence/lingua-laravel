<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class RevertEmailUpdateMail extends Mailable
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
            subject: 'Email Update',
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

        return $this->subject('Email Update')
                ->view('mails.email-update')
                ->with('emailData', $this->emailData);
    }
}
