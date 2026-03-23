<?php

namespace App\Mail;

use App\Models\Meeting;
use App\Models\MeetingMinute;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class MeetingMinutesMail extends Mailable
{
    use Queueable, SerializesModels;

    public $meeting;
    public $minutes;

    public function __construct(Meeting $meeting, MeetingMinute $minutes)
    {
        $this->meeting = $meeting;
        $this->minutes = $minutes;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Записник од состанок: ' . $this->meeting->title,
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.meeting-minutes',
        );
    }
}
