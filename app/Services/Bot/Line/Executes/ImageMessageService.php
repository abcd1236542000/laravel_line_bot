<?php

namespace App\Services\Bot\Line\Executes;

use App\Services\Bot\Line\AbstractExecuteService;
use App\Services\Bot\Line\IExecuteService;
use LINE\LINEBot\Event\BaseEvent;
use Log;

/**
 * 檔案訊息
 */
class ImageMessageService extends AbstractExecuteService implements IExecuteService
{	
    public function exec(BaseEvent $event)
    {
        $reply_token = $event->getReplyToken();
        $event_source_id = $event->getEventSourceId();
    	$source_type = $this->checkSourceType($event); // user 、 group 、room Unknown 

        $contentProvider = $event->getContentProvider();
        $isLine = $contentProvider->isLine();
        $isExternal = $contentProvider->isExternal();
        $originalContentUrl = $contentProvider->getOriginalContentUrl();
        $previewImageUrl = $contentProvider->getPreviewImageUrl();
        
        $message = '';
        $message .= chr(0x0D).chr(0x0A).' __CLASS__ => '.__CLASS__;
        $message .= chr(0x0D).chr(0x0A).' source_type => '.$source_type;
        $message .= chr(0x0D).chr(0x0A).' contentProvider isLine=> '.$isLine;
        $message .= chr(0x0D).chr(0x0A).' contentProvider isExternal=> '.$isExternal;
        $message .= chr(0x0D).chr(0x0A).' contentProvider originalContentUrl=> '.$originalContentUrl;
        $message .= chr(0x0D).chr(0x0A).' contentProvider previewImageUrl=> '.$previewImageUrl;

        $res = $this->doReplyText($reply_token, $message);     


        
    }

}
  