<?php

namespace App\Helper;

trait ResponseMaker
{
    public function responseMaker($data = null, $http_code = 200, $message = null)
    {
        if (empty($message)) {
            switch ($http_code) {
                case 200: // 成功
                    $message = 'OK';
                    break;
                case 202: // 已接受請求，尚未處理，有可能在處理時被拒絕
                    $message = 'Accepted';
                    break;
                case 204: // 請求成功，但沒有返回內容
                    $message = 'No Content';
                    break;
                case 205: // 請求成功，但沒有返回內容，請重新整理畫面
                    $message = 'Reset Content';
                    break;
                case 400: // 參數不足
                    $message = 'Bad Request';
                    break;
                case 401: // 權限不足 未登入
                    $message = 'Unauthorized';
                    break;
                case 403: // Forbidden
                    $message = 'Forbidden';
                    break;
                case 405: // 方法不允許，或是沒有安排此方法
                    $message = 'Method Not Allowed';
                    break;
                case 500: // 伺服器遇到了一個未曾預料的狀況，導致了它無法完成對請求的處理
                    $message = 'Internal Server Error';
                    break;
                case 999: // Forbidden
                    $message = 'Error';
                    break;
            }
        }
        $time_diff = microtime(true) - LARAVEL_START;
        return response()->json([
            'message' => $message,
            'data'    => $data,
            'duration' => $time_diff
        ], $http_code)->header('Content-Type', 'application/json');
    }
}
