<?php

namespace App\Services\Bot\Line\Traits;
use LINE\LINEBot;

trait TraitAccountService
{
    public function getProfile(LINEBot $bot, $line_user_id)
    {
        $rtn = ["result" => false, "data" => null, "message" => ''];
        $response = $this->bot->getProfile($line_user_id);
        $data = $response->getJSONDecodedBody();
        // dump($response->getJSONDecodedBody());
        if ($response->isSucceeded()) {            
            $rtn['result'] = true;
            $rtn['data'] = $data;
            // Log::info("userId => ".$data['userId']);
            // Log::info("pictureUrl => ".$data['pictureUrl']);
            // Log::info("language => ".$data['language']);
            // Log::info("displayName => ".$data['displayName']);
            // Log::info('getProfile:Succeeded');
        }else{
            // Log::info('getProfile:failed');
            // Log::info('http_status '.$response->getHTTPStatus().' raw_body'.$response->getRawBody());
            $rtn['message'] = data_get($data, 'message');
        }
        return $rtn;
    }
    public function doCreateLinkToken(LINEBot $bot, $line_user_id)
    {
        $rtn = ["result" => false, "data" => null, "message" => ''];
        $response = $this->bot->createLinkToken($line_user_id);
        $data = $response->getJSONDecodedBody();
        if ($response->isSucceeded()) {            
            $rtn['result'] = true;
            $rtn['data'] = $data;
            // Log::info("userId => ".$data['userId']);
            // Log::info("pictureUrl => ".$data['pictureUrl']);
            // Log::info("language => ".$data['language']);
            // Log::info("displayName => ".$data['displayName']);
            // Log::info('doCreateLinkToken:Succeeded');
        }else{
            // Log::info('doCreateLinkToken:failed');
            // Log::info('http_status '.$response->getHTTPStatus().' raw_body'.$response->getRawBody());
            $rtn['message'] = data_get($data, 'message');
        }
        return $rtn;
    }
}