<?php

namespace App\Services\Bot\Line\Executes;

use App\Services\Bot\Line\AbstractExecuteService;
use App\Services\Bot\Line\IExecuteService;
use LINE\LINEBot\Event\BaseEvent;

/**
 * Fles Message 回復訊息
 */
class PostbackEventService extends AbstractExecuteService implements IExecuteService
{	
    public function exec(BaseEvent $event)
    {
        $reply_token = $event->getReplyToken();
        $event_source_id = $event->getEventSourceId();
    	$source_type = $this->checkSourceType($event); // user 、 group 、room Unknown 

        $PostbackData = $event->getPostbackData();
        $PostbackParams = $event->getPostbackParams();

        $message = '';
        $message .= chr(0x0D).chr(0x0A).' __CLASS__ => '.__CLASS__;
        $message .= chr(0x0D).chr(0x0A).' source_type => '.$source_type;
        $message .= chr(0x0D).chr(0x0A).' PostbackData=> '.print_r($PostbackData, true);
        $message .= chr(0x0D).chr(0x0A).' PostbackParams=> '.print_r($PostbackParams, true);

        $res = $this->doReplyText($reply_token, $message);     
    }

}

