<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Exception;
use Carbon\Carbon;

use App\Services\Notifications\Line\NotifyService;
use App\Repositories\LineHookRecordRepository;
use App\Repositories\LineNotifyTokenRepository;

class LineNotificationController extends Controller
{
    protected $notifyService;
    protected $lineHookRecordRepository;
    protected $lineNotifyTokenRepository;

    public function __construct(
        NotifyService $notifyService,
        LineHookRecordRepository $lineHookRecordRepository,
        LineNotifyTokenRepository $lineNotifyTokenRepository
    ) {
        $this->notifyService = $notifyService;
        $this->lineHookRecordRepository = $lineHookRecordRepository;
        $this->lineNotifyTokenRepository = $lineNotifyTokenRepository;
	}
    public function getAuthorizeUrl(Request $request)
    {
        //TODO 請求及回應驗證用
        $state = "test";
        return '<a href="' . $this->notifyService->generateAuthorizeUrl($state) . '" >綁定Line Notify</a>';
    }
    public function callbackLineNotifyAuthorize(Request $request)
    {
        try {
            //NOTE 寫入DB
            $record_data = [
                'type' => $this->lineHookRecordRepository->getModel()::TYPE_NOTIFY_AUTH, 
                'content' => collect($request->all())->toJson(),
                'user_agent' => $request->header("user-agent"),
                'ip_address' => $request->ip(),
            ];
            $this->lineHookRecordRepository->create($record_data);

            $code = data_get($request, 'code', false);
            if (!$code) {
                return $this->responseMaker(null, 400, data_get($request, 'error_description', false));
            }

            //TODO 請求及回應驗證用
            $state = data_get($request, 'state', '');


            // //取得token
            $access_token_info = $this->notifyService->getAccessToken($code);
            if ($access_token_info['result'] == false) {
                return $this->responseMaker(null, 400, $access_token_info['message']);
            }

            $token = data_get($access_token_info, 'data.token');
            // TODO 綁定操作


            //NOTE 確認token狀態
            $status_info = $this->notifyService->getStatus($token);
            if ($status_info['result'] == false) {
                return $this->responseMaker(null, 400, '取得status錯誤');
            }

            //NOTE 寫入token 到DB
            $line_notify_token_data = [
                'token' => $token,
                'target_type' => data_get($status_info, 'data.targetType'),
                'target' => data_get($status_info, 'data.target'),
                'enabled' => 1,
            ];
            $this->lineNotifyTokenRepository->create($line_notify_token_data);

            //NOTE 發送一則測試推播
            $line_notify_message = PHP_EOL . '系統執行時間 : ' . Carbon::now()->toDateTimeString();
            $line_notify_message .= PHP_EOL . '綁定完成';
            $push_result = $this->notifyService->pushNotify(['message' => $line_notify_message], $token);



            return $this->responseMaker(null, 200);
        } catch (Exception $e) {
            return $this->responseMaker(null, 403, $e->getMessage());
        }
    }
}
