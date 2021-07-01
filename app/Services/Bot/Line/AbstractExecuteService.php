<?php

namespace App\Services\Bot\Line;

use App\Services\Bot\Line\Traits\TraitAccountService;
use App\Services\Bot\Line\Traits\TraitMessageService;
use App\Services\Bot\Line\Traits\TraitWebhookService;
use Log;

use LINE\LINEBot\Event\BaseEvent;
use LINE\LINEBot;
use LINE\LINEBot\MessageBuilder\TextMessageBuilder;
use LINE\LINEBot\SenderBuilder\SenderMessageBuilder;

abstract class AbstractExecuteService implements IExecuteService
{
    use TraitAccountService, TraitMessageService, TraitWebhookService;
    protected $bot;
    // public function __construct(LINEBot $bot)
    // {
    //     $this->bot = $bot;
    // }
    public function initBot(LINEBot $bot)
    {
        $this->bot = $bot;
    }
    //來源分類
    public function checkSourceType(BaseEvent $event)
    {
        //事件類型
        $event_type = null;
        if($event->isUserEvent()){
            $event_type = "user";
        }elseif ($event->isGroupEvent()){
            $event_type = "group";
        }elseif($event->isRoomEvent()){
            $event_type = "room";
        }elseif($event->isUnknownEvent()){
            $event_type = "unknown";
        }
        return $event_type;
    }

    public function doGetWebhookEndpointInfo()
    {        
        $rtn = ["result" => false, "data" => null, "message" => ''];
        $data = $this->doRequestWebhookEndpointInfo($this->bot);
        if ($data["result"]) {            
            $rtn["result"] = true;
            $rtn["data"] = data_get($data, "data");
        }else{
            $rtn["message"] = data_get($data, 'message');
        }
        return $rtn;
    }

    public function doSetWebhookEndpointUrl($url)
    {        
        $rtn = ["result" => false, "data" => null, "message" => ''];
        $data = $this->doSetWebhookEndpoint($this->bot, $url);
        if ($data["result"]) {            
            $rtn["result"] = true;
            $rtn["data"] = data_get($data, "data");
        }else{
            $rtn["message"] = data_get($data, 'message');
        }
        return $rtn;
    }

    public function doTestWebhookEndpointUrl($url)
    {        
        $rtn = ["result" => false, "data" => null, "message" => ''];
        $data = $this->doTestWebhookEndpoint($this->bot, $url);
        if ($data["result"]) {            
            $rtn["result"] = true;
            $rtn["data"] = data_get($data, "data");
        }else{
            $rtn["message"] = data_get($data, 'message');
        }
        return $rtn;
    }

    public function doPushMessage($to = null, $text = null)
    {        
        $rtn = ["result" => false, "data" => null, "message" => ''];
        $textMessageBuilder = new TextMessageBuilder($text);
        $data = $this->doSendPushMessage($this->bot, $to, $textMessageBuilder);
        if ($data["result"]) {            
            $rtn["result"] = true;
            $rtn["data"] = data_get($data, "data");
        }else{
            $rtn["message"] = data_get($data, 'message');
        }
        return $rtn;
    }

    public function doReplyText($reply_token = null, $text = null)
    {        
        $rtn = ["result" => false, "data" => null, "message" => ''];
        $textMessageBuilder = new TextMessageBuilder($text);
        $data = $this->doSendReplyMessage($this->bot, $reply_token, $textMessageBuilder);
        if ($data["result"]) {            
            $rtn["result"] = true;
            $rtn["data"] = data_get($data, "data");
        }else{
            $rtn["message"] = data_get($data, 'message');
        }
        return $rtn;
    }

    public function doReplyTextWithSender($reply_token = null, $text = null, $sender_data = null)
    {   
        $rtn = ["result" => false, "data" => null, "message" => ''];
        $sender = null;
        if(!empty($sender_data)){
            $sender = new SenderMessageBuilder(data_get($sender_data, 'name'), data_get($sender_data, 'iconUrl'));
        }        
        $textMessageBuilder = new TextMessageBuilder($text, $sender);
        $data = $this->doSendReplyMessage($this->bot, $reply_token, $textMessageBuilder);
        if ($data["result"]) {            
            $rtn["result"] = true;
            $rtn["data"] = data_get($data, "data");
        }else{
            $rtn["message"] = data_get($data, 'message');
        }
        return $rtn;
    }
    
    public function doReplyTextWithSenderAndQuickReply($reply_token = null, $text = null, $sender_data = null, $quick_reply = null)
    {  
        
        $sender = null;
        if(!empty($sender_data)){
            $sender = new SenderMessageBuilder(data_get($sender_data, 'name'), data_get($sender_data, 'iconUrl'));
        }
        
        $quickReply = null;
        $quickReply = new \LINE\LINEBot\QuickReplyBuilder\QuickReplyMessageBuilder([
            new \LINE\LINEBot\QuickReplyBuilder\ButtonBuilder\QuickReplyButtonBuilder(new \LINE\LINEBot\TemplateActionBuilder\MessageTemplateActionBuilder('LabelText', 'Text66'), 'https://foo.bar'),
        ]);

        $textMessageBuilder = new TextMessageBuilder($text, $sender, $quickReply);

        $data = $this->doSendReplyMessage($this->bot, $reply_token, $textMessageBuilder);
        if ($data["result"]) {            
            $rtn["result"] = true;
            $rtn["data"] = data_get($data, "data");
        }else{
            $rtn["message"] = data_get($data, 'message');
        }
        return $rtn;
    }
}