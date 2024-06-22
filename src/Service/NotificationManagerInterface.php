<?php

namespace App\Service;

use App\Entity\Event;
use App\Entity\User;

interface NotificationManagerInterface
{

    public function sendRegistrationConfirmation(User $to, Event $event): void;
    public function sendRegistrationCancellation(User $to, Event $event): void;

}