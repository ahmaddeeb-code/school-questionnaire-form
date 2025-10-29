<?php
namespace App\Helpers;

class MailStub
{
    public static function send(string $to, string $subject, string $body): void
    {
        error_log("Mail stub => To: {$to} | Subject: {$subject} | Body: {$body}");
    }
}
