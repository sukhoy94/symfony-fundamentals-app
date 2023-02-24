<?php

namespace App\MessageHandler;

use App\Message\SmsNotification;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
final class SmsNotificationHandler
{
    public function __invoke(SmsNotification $message): void
    {
        dd($message);
    }
}
