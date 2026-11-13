<?php

namespace App\Mail;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class OrderMail extends Mailable
{
    use Queueable, SerializesModels;

    public const TEMPLATES = [
        'diterima' => 'Tempahan diterima',
        'pickup' => 'Pickup dijadual',
        'hantar' => 'Karpet dalam perjalanan',
        'selesai' => 'Resit',
    ];

    public function __construct(public Order $order, public string $template) {}

    public static function subjectFor(string $template, string $orderNumber): string
    {
        return (self::TEMPLATES[$template] ?? 'Kemas kini order').' · '.$orderNumber;
    }

    public function envelope(): Envelope
    {
        return new Envelope(subject: self::subjectFor($this->template, $this->order->number));
    }

    public function content(): Content
    {
        return new Content(markdown: 'mail.order', with: [
            'order' => $this->order,
            'template' => $this->template,
            'whatsapp' => config('cucikarpet.whatsapp_display'),
        ]);
    }
}
