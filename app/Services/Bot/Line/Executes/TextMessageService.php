<?php

namespace App\Services\Bot\Line\Executes;

use App\Services\Bot\Line\AbstractExecuteService;
use App\Services\Bot\Line\IExecuteService;
use LINE\LINEBot\Event\BaseEvent;
use Log;

/**
 * 文字訊息
 */
class TextMessageService extends AbstractExecuteService implements IExecuteService
{
    public function exec(BaseEvent $event)
    {
        $reply_token = $event->getReplyToken();
        $event_source_id = $event->getEventSourceId();
    	$source_type = $this->checkSourceType($event); // user 、 group 、room Unknown 
        $text = $event->getText();
        // Log::info('__CLASS__ => '.__CLASS__);
        // Log::info('reply_token => '.$reply_token);
        // Log::info('source_type => '.$source_type);
        // Log::info('event_source_id => '.$event_source_id);

        $res = $this->doReplyText($reply_token, $text);
        // Log::info('res => ', $res);

        //回覆者賦予 名稱 以及 icon 
        // $sender_data = null;
        // $sender_data = ['name' => '豹', 'iconUrl' => 'https://images.pexels.com/photos/8128557/pexels-photo-8128557.jpeg'];
        // $this->doReplyTextWithSender($reply_token, $text, $sender_data);
        
        // 回覆者賦予 名稱 以及 icon 以及 快速回覆 
        // $sender_data = null;
        // $sender_data = ['name' => '豹', 'iconUrl' => 'https://images.pexels.com/photos/8128557/pexels-photo-8128557.jpeg'];
        // $this->doReplyTextWithSenderAndQuickReply($reply_token, $text, $sender_data);
        
        // 推播
        // $line_user_id = ""
        // $line_group_id = ""
        // $this->doPushMessage($line_user_id, $text);//個人
        // $this->doPushMessage($line_group_id, $text);//群組
        
        // 全體廣播
        // $text_message_builder = new \LINE\LINEBot\MessageBuilder\TextMessageBuilder("Hello");
        // $data = $this->doAnyBroadcastMessage($this->bot, $text_message_builder);
        
        // 全體推播
        // $text_message_builder = new \LINE\LINEBot\MessageBuilder\TextMessageBuilder("Hello");
        // $data = $this->doMulticastMessage($this->bot, [$line_user_id], $text_message_builder);

    }

}