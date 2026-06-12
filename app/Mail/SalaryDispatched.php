<?php

namespace App\Mail;

use App\Models\Member;
use App\Models\PayrollPeriod;
use App\Models\PayrollRun;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class SalaryDispatched extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public $tries = 3;

    public $backoff = [60, 120, 300];

    public function __construct(
        public Member $member,
        public PayrollRun $run,
        public PayrollPeriod $period,
    ) {
        $this->afterCommit();
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Your Salary Has Been Dispatched',
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.payroll.salary-dispatched',
            with: [
                'member' => $this->member,
                'run' => $this->run,
                'period' => $this->period,
                'monthName' => now()->setMonth($this->period->month)->format('F'),
            ],
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
