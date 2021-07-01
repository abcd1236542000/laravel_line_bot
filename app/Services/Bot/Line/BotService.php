<?php

namespace App\Services\Bot\Line;

use Illuminate\Http\Request;
use Log;
use App\Repositories\LineAccountsRepository;

use LINE\LINEBot\Exception\InvalidEventRequestException;
use LINE\LINEBot\Exception\InvalidSignatureException;
use LINE\LINEBot\HTTPClient\CurlHTTPClient;
use LINE\LINEBot;

use LINE\LINEBot\Event\BaseEvent;

use LINE\LINEBot\MessageBuilder\TextMessageBuilder;


class BotService
{
    protected $channel_access_token;
    protected $channel_secret;
    protected $line_http_client;
    protected $line_bot;

    protected $lineAccountsRepository;
    protected $lineAccountChannelsRepository;

    public function __construct(
        LineAccountsRepository $lineAccountsRepository
    ) {
        $this->channel_access_token =  env('LINE_MESSAGE_CHANNEL_ACCESS_TOKEN');
        $this->channel_secret =  env('LINE_MESSAGE_CHANNEL_SECRET');
        $this->line_http_client = new CurlHTTPClient($this->channel_access_token);
        $this->lineBot = new LINEBot($this->line_http_client, ['channelSecret' => $this->channel_secret]);
        $this->lineAccountsRepository = $lineAccountsRepository;
    }

    //處理事件分類
    public function handleEvent(BaseEvent $event)
    {
        // 取 vendor 裡的 LINE\LINEBot\Event\ 相對應事件
        $event_class_name = (new \ReflectionClass($event))->getShortName();
        // Log::info("event_class_name");Log::info($event_class_name);
        // $event_class_name = "Sample";
        $class_path = __NAMESPACE__ . '\\' . 'Executes' . '\\' . $event_class_name . 'Service';
        if (class_exists($class_path)) {
            // $class = new $class_path($this->lineBot);
            // return $class->exec($event);
            $class = app($class_path);
            $class->initBot($this->lineBot);
            return $class->exec($event);
        } else {
            Log::info("handleEvent class does not exist");
            Log::info($class_path);
            return;
        }
    }

    //處理webhook
    public function handleWebhook(Request $request)
    {
        $rtn = ['result' => false, 'message' => '', 'data' => null];
        try {

            //內容
            $request_content = $request->getContent();
            // Log::info('getContent');Log::info($request_content);

            //驗簽章 + 解析事件       
            $events = $this->lineBot->parseEventRequest($request_content, $request->header('x-line-signature'));
            // Log::info('events');Log::info($events);

            foreach ($events as $event) {
                $this->handleEvent($event);
            }
            $rtn['result'] = true;
        } catch (InvalidSignatureException $e) {
            Log::info($e->getMessage());
            $rtn['message'] = $e->getMessage();
        } catch (InvalidEventRequestException $e) {
            Log::info($e->getMessage());
            $rtn['message'] = $e->getMessage();
        }
        return $rtn;
    }
}
