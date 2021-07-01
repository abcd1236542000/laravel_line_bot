<?php

namespace App\Services\Bot\Line\Executes;

use App\Services\Bot\Line\AbstractExecuteService;
use App\Services\Bot\Line\IExecuteService;
use LINE\LINEBot\Event\BaseEvent;
use Log;

/**
 * 加到群組
 */
class JoinEventService extends AbstractExecuteService implements IExecuteService
{
    public function exec(BaseEvent $event)
    {
        $reply_token = $event->getReplyToken();
        $event_source_id = $event->getEventSourceId();
    	$source_type = $this->checkSourceType($event); // user 、 group 、room Unknown 
        Log::info('__CLASS__ => '.__CLASS__);
        Log::info('reply_token => '.$reply_token);
        Log::info('source_type => '.$source_type);
        Log::info('event_source_id => '.$event_source_id);
    }

}