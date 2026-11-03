<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Mail\Mailables\Attachment;



class ControlExtintorReportMail extends Mailable
{
 use Queueable, SerializesModels;

    public $nombreCliente;
    public $nombreLocalidad;
    public $fileName;

    /**
     * Create a new message instance.
     */
    public function __construct($nombreCliente,$nombreLocalidad, $fileName)
    {
        $this->nombreCliente = $nombreCliente;
        $this->nombreLocalidad = $nombreLocalidad;
        $this->fileName = $fileName;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Reporte de Control de Extintores'
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
         return new Content(
            view: 'emails.control_extintores',
            with: [
                'nombreCliente' => $this->nombreCliente,
                'nombreLocalidad' => $this->nombreLocalidad,
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
            Attachment::fromPath(storage_path('app/' . $this->fileName))
                      ->as('control_extintores.xlsx')
                      ->withMime('application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'),
        ];
    }

}
