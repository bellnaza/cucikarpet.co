<?php

namespace App\Jobs;

use App\Mail\OrderMail;
use App\Models\EmailLog;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Throwable;

/** Sends one queued customer email through the configured mailer (Resend in production). */
class SendOrderEmail implements ShouldQueue
{
    use Queueable;

    public int $tries = 3;

    public array $backoff = [60, 300];

    public function __construct(public int $emailLogId) {}

    public function handle(): void
    {
        $log = EmailLog::with(['order.customer', 'order.items.carpetType'])->find($this->emailLogId);
        if (! $log || $log->status === 'sent' || ! $log->order) {
            return;
        }

        try {
            Mail::to($log->to)->send(new OrderMail($log->order, $log->template));
            $log->update(['status' => 'sent', 'sent_at' => now(), 'error' => null]);
        } catch (Throwable $e) {
            $log->update(['status' => 'failed', 'error' => Str::limit($e->getMessage(), 500)]);
            throw $e;
        }
    }
}
