<?php

namespace App\Mail;

use App\Models\Penalty;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class PenaltyApplied extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public function __construct(
        public Penalty $penalty,
    ) {
        $this->afterCommit();
    }

    public function envelope(): Envelope
    {
        $label = $this->penalty->type === 'late_meeting' ? 'Late Meeting Penalty' : 'Loss of BGG Identity Card Penalty';

        return new Envelope(
            subject: "Penalty Applied: {$label}",
        );
    }

    public function content(): Content
    {
        $this->penalty->loadMissing('member.user', 'appliedBy');

        $label = $this->penalty->type === 'late_meeting' ? 'Late Meeting Penalty' : 'Loss of BGG Identity Card Penalty';

        return new Content(
            markdown: 'emails.penalty-applied',
            with: [
                'penalty' => $this->penalty,
                'label' => $label,
                'firstName' => $this->penalty->member->user->first_name ?? 'Member',
            ],
        );
    }
}
