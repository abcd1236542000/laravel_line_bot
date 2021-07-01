<?php

namespace App\Services\Bot\Line\Executes;

use App\Services\Bot\Line\AbstractExecuteService;
use App\Services\Bot\Line\IExecuteService;
use LINE\LINEBot\Event\BaseEvent;
use LINE\LINEBot\MessageBuilder\LocationMessageBuilder;
use Log;

/**
 * 定位
 */
class LocationMessageService extends AbstractExecuteService implements IExecuteService
{	
    public function exec(BaseEvent $event)
    {
        $reply_token = $event->getReplyToken();
        $event_source_id = $event->getEventSourceId();
    	$source_type = $this->checkSourceType($event); // user 、 group 、room Unknown 

        $Title = $event->getTitle();
        $Address = $event->getAddress();
        $Latitude = $event->getLatitude();
        $Longitude = $event->getLongitude();

        $message = '';
        $message .= chr(0x0D).chr(0x0A).' __CLASS__ => '.__CLASS__;
        $message .= chr(0x0D).chr(0x0A).' source_type => '.$source_type;

        $message .= chr(0x0D).chr(0x0A).' Title=> '.$Title;
        $message .= chr(0x0D).chr(0x0A).' Address=> '.$Address;
        $message .= chr(0x0D).chr(0x0A).' Latitude=> '.$Latitude;
        $message .= chr(0x0D).chr(0x0A).' Longitude=> '.$Longitude;

        $res = $this->doReplyText($reply_token, $message);     
        // $this->doSendReplyMessage($this->bot, $reply_token, new LocationMessageBuilder('Location test', 'Tokyo Shibuya', 35.6566285, 139.6999638));


        
    }

}
  