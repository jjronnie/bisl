<?php

namespace App\Mail;

use App\Models\Loan; // Assuming your model is App\Models\Loan
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class PaymentReceived extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * The Loan instance.
     *
     * @var \App\Models\Loan
     */
    public $loan;

    /**
     * The amount that was paid in this transaction.
     *
     * @var float
     */
    public $amountPaid;

    /**
     * Create a new message instance.
     *
     * @param Loan $loan
     * @param float $amountPaid
     */
    public function __construct(Loan $loan, float $amountPaid)
    {
        // Load the member/user relationship if not already loaded
        $loan->loadMissing('member.user');
        $this->loan = $loan;
        $this->amountPaid = $amountPaid;
    }

    /**
     * Get the message envelope.
     *
     * @return \Illuminate\Mail\Mailables\Envelope
     */
    public function envelope()
    {
        return new Envelope(
            subject: 'Payment Confirmation: Loan #' . $this->loan->loan_number,
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
            markdown: 'emails.loan.payment', // We create this Blade file next
            with: [
                'loan' => $this->loan,
                'amountPaid' => $this->amountPaid,
            ]
        );
    }
}