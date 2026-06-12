<?php

namespace App\Mail;

use App\Models\LoanInstallment;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class LoanPenaltyApplied extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public function __construct(
        public LoanInstallment $installment,
    ) {
        $this->afterCommit();
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Penalty Applied on Loan #'.$this->installment->loan->loan_number,
        );
    }

    public function content(): Content
    {
        $this->installment->loadMissing('loan.member.user');

        return new Content(
            markdown: 'emails.loan.penalty-applied',
            with: [
                'installment' => $this->installment,
                'loan' => $this->installment->loan,
                'firstName' => $this->installment->loan->member->user->first_name ?? 'Member',
            ],
        );
    }
}
