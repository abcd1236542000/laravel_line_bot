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

        //TODO 根據特定詞彙處理
        switch ($text) {
            //NOTE 限定A
            case (preg_match("/(A)+$/", $text) ? true : false) :
                $res = $this->doReplyText($reply_token, $text);
                break;
            //NOTE 測試中 全家物流 單號查詢 famiport [fp-eco::ec_order] 
            case (preg_match("/fp-eco::/i", $text) ? true : false) :
                $ec_order = str_replace("fp-eco::","",$text);
                $message = app('App\Services\Test\FamiportService')->getOrderInfo($ec_order);
                $res = $this->doReplyText($reply_token, $message);
                break;
            //NOTE 測試中 全家物流 ec單號&全家單號 查詢 famiport  [fp-eco-ord::ec_order|order_no]
            case (preg_match("/fp-eco-ord::/i", $text) ? true : false) :
                $order_str = str_replace("fp-eco-ord::","",$text);
                $exp_data = explode('|', $order_str);
                $order_detail_info = app('App\Services\Test\FamiportService')->getOrderDetail($exp_data[0], $exp_data[1]);
                $d_data_str = data_get($order_detail_info, 'd');
                $d_data = json_decode($d_data_str, true);
                $list = data_get($d_data, 'List');
                $message = "ORDER_NO(".$exp_data[1].")".PHP_EOL."EC_ORDER_NO(".$exp_data[0].")".PHP_EOL;
                foreach ($list as $data) {
                    $message .= $data['SEND_STORE_NAME'].'-'.$data['RCV_STORE_NAME'].'-'.$data['RCV_STORE_ADDRESS'].' => '.$data['STATUS_D'].'('.$data['ORDER_DATE_R'] .')'. PHP_EOL;
                }
                $res = $this->doReplyText($reply_token, $message);
                break;
            default :
                $res = $this->doReplyText($reply_token, $text);
                break;
        }
        
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