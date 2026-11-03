<?php

namespace App\Services;

use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Illuminate\Mail\Mailable;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\View;

class EmailService
{
    public function sendEmailWithAttachment(array $to, string $subject, string $view, array $viewData = [], ?string $filePath = null, ?string $fileName = null)
    {
        foreach ($to as $recipient) {
            Mail::send($view, $viewData, function ($message) use ($recipient, $subject, $filePath, $fileName) {
                $message->to($recipient)->subject($subject);

                if ($filePath && Storage::exists($filePath)) {
                    $fullPath = storage_path('app/' . $filePath);
                    $mimeType = mime_content_type($fullPath) ?: 'application/octet-stream';

                    $message->attach($fullPath, [
                        'as' => $fileName ?? basename($filePath),
                        'mime' => $mimeType,
                    ]);
                }
            });
        }
    }

    public function sendEmail(array $to, string $subject, string $view, array $viewData = [])
    {
        foreach ($to as $recipient) {
            Mail::send($view, $viewData, function ($message) use ($recipient, $subject) {
                $message->to($recipient)->subject($subject);
            });
        }
    }

}
