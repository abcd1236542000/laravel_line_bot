<?php

namespace App\Services\Bot\Line\Traits;
use LINE\LINEBot;

trait TraitWebhookService
{
    
    public function doRequestWebhookEndpointInfo(LINEBot $bot)
    {
        $rtn = ["result" => false, "data" => null, "message" => ''];
        $response = $bot->getWebhookEndpointInfo();
        $data = $response->getJSONDecodedBody();
        if ($response->isSucceeded()) {            
            $rtn['result'] = true;
            $rtn['data'] = $data;
            // Log::info('doRequestWebhookEndpointInfo:Succeeded');
        }else{
            // Log::info('doRequestWebhookEndpointInfo:failed');
            // Log::info($data);
            $rtn['message'] = data_get($data, 'message');
        }
        return $rtn;
    }
    
    public function doSetWebhookEndpoint(LINEBot $bot, $url)
    {
        $rtn = ["result" => false, "data" => null, "message" => ''];
        $response = $bot->setWebhookEndpoint($url);
        $data = $response->getJSONDecodedBody();
        if ($response->isSucceeded()) {            
            $rtn['result'] = true;
            $rtn['data'] = $data;
            // Log::info('doSetWebhookEndpoint:Succeeded');
        }else{
            // Log::info('doSetWebhookEndpoint:failed');
            // Log::info($data);
            $rtn['message'] = data_get($data, 'message');
        }
        return $rtn;
    }
    
    public function doTestWebhookEndpoint(LINEBot $bot, $url)
    {
        $rtn = ["result" => false, "data" => null, "message" => ''];
        $response = $bot->testWebhookEndpoint($url);
        $data = $response->getJSONDecodedBody();
        if ($response->isSucceeded()) {            
            $rtn['result'] = true;
            $rtn['data'] = $data;
            // Log::info('doTestWebhookEndpoint:Succeeded');
        }else{
            // Log::info('doTestWebhookEndpoint:failed');
            // Log::info($data);
            $rtn['message'] = data_get($data, 'message');
        }
        return $rtn;
    }
}