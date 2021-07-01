<?php

namespace App\Services\Bot\Line\Executes;

use App\Services\Bot\Line\AbstractExecuteService;
use App\Services\Bot\Line\IExecuteService;
use App\Repositories\LineAccountsRepository;
use LINE\LINEBot\Event\BaseEvent;
use Log;

/**
 * 加到官方帳號
 */
class FollowEventService extends AbstractExecuteService implements IExecuteService
{
    private $lineAccountsRepository;
    public function __construct(LineAccountsRepository $lineAccountsRepository)
    {
        $this->lineAccountsRepository = $lineAccountsRepository;

    }
    public function exec(BaseEvent $event)
    {
        $event_source_id = $event->getEventSourceId();
        // Log::info('__CLASS__ => '.__CLASS__);
        // Log::info('source_type => '.$source_type);
        // Log::info('event_source_id => '.$event_source_id);
        $profile = $this->getProfile($this->bot, $event_source_id);
        $input = [];
        if($profile["result"] === true){
            $res = $this->lineAccountsRepository->updateOrCreate([
                "line_user_id" => $event_source_id, 
            ] ,[
                'enabled' => 1, 
                'line_user_id' => $event_source_id, 
                'line_display_name' => data_get($profile, "data.displayName", null), 
            ]);
            // Log::info('res');
            // Log::info($res);
        }else{
            Log::info('doFollow');
            Log::info($profile);
        }
    }

}