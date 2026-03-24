<?php

namespace App\Mail;

use App\Models\Patient;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class DailyPatientReportMail extends Mailable
{
    use Queueable, SerializesModels;

    public $patient;
    public $appointments;
    public $date;

    /**
     * Create a new message instance.
     */
    public function __construct(Patient $patient, $appointments, $date)
    {
        $this->patient = $patient;
        $this->appointments = $appointments;
        $this->date = $date;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Tus Citas Programadas para Hoy (' . $this->date . ')',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.daily-patient',
            with: [
                'patient' => $this->patient,
                'appointments' => $this->appointments,
                'date' => $this->date,
            ]
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}
