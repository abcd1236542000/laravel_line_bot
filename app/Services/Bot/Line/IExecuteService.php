<?php

namespace App\Services\Bot\Line;
use LINE\LINEBot\Event\BaseEvent;

interface IExecuteService
{
    public function exec(BaseEvent $event);
}