<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Exception;

use App\Repositories\LineHookRecordRepository;
use App\Services\Bot\Line\BotService;

class LineBotController extends Controller
{
	private $botService;
    protected $lineHookRecordRepository;
    public function __construct(
        BotService $botService,
        LineHookRecordRepository $lineHookRecordRepository
    ) {
		$this->botService = $botService;
        $this->lineHookRecordRepository = $lineHookRecordRepository;
	}

    public function webhook(Request $request)
    {
        try {
            //NOTE 寫入DB
            $record_data = [
                'type' => $this->lineHookRecordRepository->getModel()::TYPE_WEBHOOK_EVENT, 
                'content' => $request->getContent(), 
                'user_agent' => $request->header("user-agent"), 
                'ip_address' => $request->ip(),
            ];
            $this->lineHookRecordRepository->create($record_data);
            
            //NOTE 實作
            $data = $this->botService->handleWebhook($request);
            return $this->responseMaker($data);
        } catch (Exception $e) {
            return $this->responseMaker(null, 403, $e->getMessage());
        }
    }
}