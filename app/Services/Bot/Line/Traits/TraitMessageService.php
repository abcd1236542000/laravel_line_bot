<?php

namespace App\Services\Bot\Line\Traits;

use LINE\LINEBot;
use LINE\LINEBot\MessageBuilder\TextMessageBuilder;
use LINE\LINEBot\MessageBuilder\LocationMessageBuilder;
use LINE\LINEBot\MessageBuilder;
use Log;

trait TraitMessageService
{
    //廣播特定user_id(限定) / 500人
    public function doMulticastMessage(LINEBot $bot, array $tos = [], MessageBuilder $textMessageBuilder, $notificationDisabled = false, $retryKey = null)
    {
        $rtn = ["result" => false, "data" => null, "message" => ''];
        $response = $bot->multicast($tos, $textMessageBuilder, $notificationDisabled, $retryKey);
        $data = $response->getJSONDecodedBody();
        if ($response->isSucceeded()) {            
            $rtn['result'] = true;
            $rtn['data'] = $data;
            // Log::info('doMulticastMessage:Succeeded');
        }else{
            // Log::info('doMulticastMessage:failed');
            // Log::info($data);
            $rtn['message'] = data_get($data, 'message');
        }
        return $rtn;
    }

    //廣播所有user
    public function doAnyBroadcastMessage(LINEBot $bot, MessageBuilder $textMessageBuilder, $notificationDisabled = false, $retryKey = null)
    {
        $rtn = ["result" => false, "data" => null, "message" => ''];
        $response = $bot->broadcast($textMessageBuilder, $notificationDisabled, $retryKey);
        $data = $response->getJSONDecodedBody();
        if ($response->isSucceeded()) {            
            $rtn['result'] = true;
            $rtn['data'] = $data;
            // Log::info('doAnyBroadcastMessage:Succeeded');
        }else{
            // Log::info('doAnyBroadcastMessage:failed');
            // Log::info($data);
            $rtn['message'] = data_get($data, 'message');
        }
        return $rtn;
    }

    //實作 回覆類型訊息 只接受 reply_token
    public function doSendReplyMessage(LINEBot $bot, $reply_token, MessageBuilder $textMessageBuilder)
    {       
        $rtn = ["result" => false, "data" => null, "message" => ''];
        $response = $bot->replyMessage($reply_token, $textMessageBuilder);
        $data = $response->getJSONDecodedBody();
        if ($response->isSucceeded()) {            
            $rtn['result'] = true;
            $rtn['data'] = $data;
            // Log::info('doSendReplyMessage:Succeeded');
        }else{
            // Log::info('doSendReplyMessage:failed');
            // Log::info($data);
            $rtn['message'] = data_get($data, 'message');
        }
        return $rtn;
    }
    
    //實作 推播 to只接受 userId roomId groupId
    public function doSendPushMessage(LINEBot $bot, $to, MessageBuilder $textMessageBuilder, $notificationDisabled = false)
    {
        // $textMessageBuilder = new TextMessageBuilder($text);
        $response = $bot->pushMessage($to, $textMessageBuilder, $notificationDisabled);
        $data = $response->getJSONDecodedBody();
        if ($response->isSucceeded()) {            
            $rtn['result'] = true;
            $rtn['data'] = $data;
            // Log::info('doSendPushMessage:Succeeded');
        }else{
            // Log::info('doSendPushMessage:failed');
            // Log::info($data);
            $rtn['message'] = data_get($data, 'message');
        }
        return $rtn;
    }

}