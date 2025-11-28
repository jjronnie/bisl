<?php

namespace App\Mail;

use App\Models\Loan; // Assuming your model is App\Models\Loan
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class LoanStatusUpdate extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * The Loan instance.
     *
     * @var \App\Models\Loan
     */
    public $loan;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(Loan $loan)
    {
        $this->loan = $loan;
    }

    /**
     * Get the message envelope.
     *
     * @return \Illuminate\Mail\Mailables\Envelope
     */
    public function envelope()
    {
        $subject = 'Loan Application #' . $this->loan->loan_number . ' Status Update: ' . ucfirst($this->loan->status);
        return new Envelope(
            subject: $subject,
        );
    }

    /**
     * Get the message content definition.
     *
     * @return \Illuminate\Mail\Mailables\Content
     */
    public function content()
    {
        return new Content(
            markdown: 'emails.loan.status-update', // We will create this Blade file next
            with: [
                'loan' => $this->loan,
            ]
        );
    }
}