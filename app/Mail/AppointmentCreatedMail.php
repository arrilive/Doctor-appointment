<?php

namespace App\Mail;

use App\Models\Appointment;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class AppointmentCreatedMail extends Mailable
{
    use Queueable, SerializesModels;

    public $appointment;
    public $pdfContent;
    public $recipientType; // 'patient' or 'doctor'

    /**
     * Create a new message instance.
     */
    public function __construct(Appointment $appointment, $pdfContent, $recipientType)
    {
        $this->appointment = $appointment;
        // Don't serialize the raw PDF content if pushing to a queue; ideally, we'd pass a path or regenerate it in the Job.
        // However, for this requirement we are directly generating the PDF in the service.
        $this->pdfContent = $pdfContent;
        $this->recipientType = $recipientType;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        $subject = $this->recipientType === 'doctor' 
            ? 'Nueva Cita Agendada: ' . $this->appointment->patient->user->name
            : 'Appointment Confirmation';

        return new Envelope(
            subject: $subject,
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.appointment-created',
            with: [
                'appointment' => $this->appointment,
                'recipientType' => $this->recipientType,
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
        return [
            Attachment::fromData(fn () => $this->pdfContent, 'Cita_Medica.pdf')
                ->withMime('application/pdf'),
        ];
    }
}
