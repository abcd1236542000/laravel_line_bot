<?php

namespace App\Services\Notifications\Line;

use App\Services\HttpClient\CurlClientService;
use Validator;


class NotifyService
{
    protected $hashKey;
    protected $curlClientService;
    protected $client_id;
    protected $client_secret;
    protected $redirect_uri;

    public function __construct(
        CurlClientService $curlClientService
    ) {
        $this->curlClientService = $curlClientService;
        $this->client_id =  env('LINE_NOTIFY_CLIENT_ID');
        $this->client_secret =  env('LINE_NOTIFY_CLIENT_SECRET');
        $this->redirect_uri =  env('LINE_NOTIFY_REDIRECT_URI');
    }
    /**
     * [generateAuthorizeUrl 取得綁定網址]
     * @return [string] [url]
     */
    public function generateAuthorizeUrl($state = 'state')
    {
        $url = 'https://notify-bot.line.me/oauth/authorize';
        $rtn = '';
        $param = [];
        $param['response_type'] = 'code';
        $param['scope'] = 'notify';
        $param['client_id'] = $this->client_id;
        $param['redirect_uri'] = $this->redirect_uri;
        $param['state'] = $state;        
        $rtn .= $url . '?' . http_build_query($param);
        return $rtn;
    }

    public function getAccessToken($code)
    {
        $url = 'https://notify-bot.line.me/oauth/token';
        $rtn = ['result' => false, 'data' => null, 'message' => ''];
        $param = [];
        $param['grant_type'] = 'authorization_code';
        $param['code'] = $code;
        $param['client_id'] = $this->client_id;
        $param['client_secret'] = $this->client_secret;
        $param['redirect_uri'] = $this->redirect_uri;

        //是否忽略SSL
        $sslverify = (env('APP_ENV', '') == 'local')  ? false : true;
        $requestData = $this->curlClientService->doRequestJsonToArray($url, ['form_params' => $param], 'POST', $sslverify);
        if ($requestData['status'] == 200) {
            $rtn['result'] = true;
            $rtn['data']['token'] = $requestData['access_token'];
        } else {
            $rtn['message'] = $requestData['message'];
        }
        return $rtn;
    }

    public function getStatus($token)
    {
        $url = 'https://notify-api.line.me/api/status';
        $rtn = ['result' => false, 'data' => null, 'message' => ''];
        $param = [];
        $param['headers'] =   ['Authorization' => 'Bearer ' . $token];
        // //是否忽略SSL
        $sslverify = (env('APP_ENV', '') == 'local')  ? false : true;
        $requestData = $this->curlClientService->doRequestJsonToArray($url, $param, 'GET', $sslverify);
        if ($requestData['status'] == 200) {
            $rtn['result'] = true;
            $rtn['data'] = $requestData;
        } else {
            $rtn['message'] = $requestData['message'];
        }
        return $rtn;
    }

    public function pushNotify($contain, $token)
    {
        $url = 'https://notify-api.line.me/api/notify';
        $rtn = ['result' => false, 'data' => null, 'message' => ''];
        //驗證資料
        $validator_data = [
            'message'  => 'required|max:1000',
            'notificationDisabled'  => 'boolean',
            'imageFile'  => 'string',

            'stickerId'  => 'required_with:stickerPackageId',
            'stickerPackageId'  => 'required_with:stickerId',

            'imageThumbnail'  => 'required_with:imageFullsize',
            'imageFullsize'  => 'required_with:imageThumbnail',
        ];
        $validator = Validator::make($contain, $validator_data);
        if ($validator->fails()) {
            $rtn['message'] = $validator->messages()->first();
            return $rtn;
        }

        $param = $this->buildRequest($contain, $token);

        // //是否忽略SSL
        $sslverify = (env('APP_ENV', '') == 'local')  ? false : true;
        $requestData = $this->curlClientService->doRequestJsonToArray($url, $param, 'POST', $sslverify);

        if ($requestData['status'] == 200) {
            $rtn['result'] = true;
            $rtn['data'] = $requestData;
        } else {
            $rtn['message'] = $requestData['message'];
        }
        return $rtn;
    }
    /**
     * [revokeAccess 解除綁定]
     * @param  [type] $token [令牌]
     * @return [type]        [description]
     */
    public function revokeAccess($token)
    {
        $url = 'https://notify-api.line.me/api/revoke';
        $rtn = ['result' => false, 'data' => null, 'message' => ''];
        $param = [];
        $param['headers'] =   ['Authorization' => 'Bearer ' . $token];
        // //是否忽略SSL
        $sslverify = (env('APP_ENV', '') == 'local')  ? false : true;
        $requestData = $this->curlClientService->doRequestJsonToArray($url, $param, 'POST', $sslverify);
        if ($requestData['status'] == 200) {
            $rtn['result'] = true;
            $rtn['data'] = $requestData;
        } else {
            $rtn['message'] = $requestData['message'];
        }
        return $rtn;
    }

    protected function convertFormToMultipart($data = [])
    {
        $multipart = [];
        foreach ($data as $name => $contents) {
            $multipart[] = compact('name', 'contents');
        }
        return $multipart;
    }

    protected function buildRequest($parame = [], $token = '')
    {
        $optionalFields = array_filter([
            //圖片連結 縮圖
            'imageThumbnail' => data_get($parame, 'imageThumbnail', null),
            //圖片連結 全圖            
            'imageFullsize' => data_get($parame, 'imageFullsize', null),

            //https://devdocs.line.me/files/sticker_list.pdf
            //官方貼圖 大類
            'stickerPackageId' => data_get($parame, 'stickerPackageId', null),
            //官方貼圖 小類
            'stickerId' => data_get($parame, 'stickerId', null),

            //提醒通知
            'notificationDisabled' => data_get($parame, 'notificationDisabled', false),
        ]);
        $message = data_get($parame, 'message', '');
        $imageFile = data_get($parame, 'imageFile', '');
        $method = data_get($parame, 'method', 'GET');

        return array_merge(file_exists($imageFile) ? [
            'multipart' => array_merge([
                [
                    'name' => 'message',
                    'contents' => $message,
                ],
                [
                    'name' => 'imageFile',
                    'contents' => fopen($imageFile, 'r'),
                ],
            ], $this->convertFormToMultipart($optionalFields))
        ] : [
            'form_params' => array_merge([
                'message' => $message
            ], $optionalFields)
        ], [
            'headers' => [
                'Authorization' => 'Bearer ' . $token
            ]
        ]);
    }
}
