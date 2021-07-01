<?php

namespace App\Services\Bot\Line\Executes;

use App\Services\Bot\Line\AbstractExecuteService;
use App\Services\Bot\Line\IExecuteService;
use LINE\LINEBot\Event\BaseEvent;

/**
 * 貼圖
 */
class StickerMessageService extends AbstractExecuteService implements IExecuteService
{	
    public function exec(BaseEvent $event)
    {
        $reply_token = $event->getReplyToken();
        $event_source_id = $event->getEventSourceId();
    	$source_type = $this->checkSourceType($event); // user 、 group 、room Unknown 

        $PackageId = $event->getPackageId();
        $StickerId = $event->getStickerId();
        $StickerResourceType = $event->getStickerResourceType();

        $message = '';
        $message .= chr(0x0D).chr(0x0A).' __CLASS__ => '.__CLASS__;
        $message .= chr(0x0D).chr(0x0A).' source_type => '.$source_type;
        $message .= chr(0x0D).chr(0x0A).' PackageId=> '.$PackageId;
        $message .= chr(0x0D).chr(0x0A).' StickerId=> '.$StickerId;
        $message .= chr(0x0D).chr(0x0A).' StickerResourceType=> '.$StickerResourceType;

        $res = $this->doReplyText($reply_token, $message);     
    }

}

