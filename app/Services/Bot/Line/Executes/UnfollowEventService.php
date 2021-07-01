<?php

namespace App\Services\Bot\Line\Executes;

use App\Services\Bot\Line\AbstractExecuteService;
use App\Services\Bot\Line\IExecuteService;
use App\Repositories\LineAccountsRepository;
use LINE\LINEBot\Event\BaseEvent;
use Log;

/**
 * 封鎖官方帳號
 */
class UnfollowEventService extends AbstractExecuteService implements IExecuteService
{
    private $lineAccountsRepository;
    public function __construct(LineAccountsRepository $lineAccountsRepository)
    {
        $this->lineAccountsRepository = $lineAccountsRepository;

    }
    public function exec(BaseEvent $event)
    {
        $event_source_id = $event->getEventSourceId();
        
        $this->lineAccountsRepository->updateOrCreate([
            "line_user_id" => $event_source_id, 
        ] ,[
            'enabled' => 0, 
            'line_user_id' => $event_source_id, 
        ]);
        // Log::info('__CLASS__ => '.__CLASS__);
        // Log::info('source_type => '.$source_type);
        // Log::info('event_source_id => '.$event_source_id);
        // Log::info('__CLASS__ => '.__CLASS__);
        // Log::info('reply_token => '.$reply_token);
        // Log::info('source_type => '.$source_type);
        // Log::info('event_source_id => '.$event_source_id);
    }

}