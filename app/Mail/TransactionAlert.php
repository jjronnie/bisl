<?php

namespace App\Mail;

use App\Models\Transaction;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class TransactionAlert extends Mailable
{
    use Queueable, SerializesModels;

    public $transaction;

    public function __construct(Transaction $transaction)
    {
        // We assume the transaction has a 'member' relationship loaded
        // or we will load it in the view.
        $this->transaction = $transaction;
    }

    public function envelope(): Envelope
    {
        $type = ucfirst($this->transaction->transaction_type);

        return new Envelope(
            subject: "Transaction Notification: {$type} Alert [{$this->transaction->reference_number}]",
        );
    }

    public function content(): Content
    {
        // Load relationships if not already loaded
        $this->transaction->loadMissing('member.user');

        // Get user's first name
        $firstName = $this->transaction->member->user->first_name ?? 'Member';

        return new Content(
            markdown: 'emails.transaction_alert',
            with: [
                'transaction' => $this->transaction,
                'firstName' => $firstName,
            ]
        );
    }
}
