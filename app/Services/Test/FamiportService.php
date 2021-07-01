<?php

namespace App\Services\Test;
use App\Services\HttpClient\CurlClientService;
use Storage;

class FamiportService
{
    public function __construct(
        CurlClientService $curlClientService
    ) {
        $this->curlClientService = $curlClientService;
    }
    public function getOrderInfo($ec_order = 0)
    {
        //TODO 抓驗證圖檔
        $save_captcha_image_info = $this->saveCaptchaImage();
        $image = $save_captcha_image_info->getBody()->getContents();

        //NOTE 驗證圖檔 存到storage
        $captcha_image_file_name = 'captcha.png';
        Storage::disk('public')->put($captcha_image_file_name, $image);
        $captcha_image_file_path = Storage::disk('public')->path($captcha_image_file_name);

        //NOTE 取出cookie 查詢驗證用
        $cookie_jar = $this->curlClientService->getCookieJar();
        $cookie = $cookie_jar->getCookieByName('ASP.NET_SessionId')->getName().'='.$cookie_jar->getCookieByName('ASP.NET_SessionId')->getValue().'; ';
        $cookie .= $cookie_jar->getCookieByName('fmeweb')->getName().'='.$cookie_jar->getCookieByName('fmeweb')->getValue();

        //TODO 上傳OCR網站
        $upload_reverse_image_info = $this->uploadReverseImage($captcha_image_file_path);
        $imgUrl = data_get($upload_reverse_image_info, 'data.image_path');
        
        //TODO OCR網站圖片轉文字
        $exec_image_to_text_info = $this->doImageToText($imgUrl);
        $ori_text = data_get($exec_image_to_text_info, 'text');

        //NOTE 處理解析文字
        $text = preg_replace('/\D/', '', $ori_text);

        //TODO 取得透過EC訂單編號 取得 全家物流訂單編號 (不知道全家物流訂單編號前提)
        $inquiry_orders_info = $this->doInquiryOrders($cookie, $ec_order, $text);
        $d_data_str = data_get($inquiry_orders_info, 'd');
        $d_data = json_decode($d_data_str, true);
        $ec_order_no = data_get($d_data, 'List.0.EC_ORDER_NO');
        $order_no = data_get($d_data, 'List.0.ORDER_NO');
        $order_message = data_get($d_data, 'List.0.ORDERMESSAGE');

        //TODO 取得透過EC訂單編號&全家物流訂單編號 取得 訂單資訊
        $order_detail_info = $this->getOrderDetail($ec_order_no, $order_no);
        $d_data_str = data_get($order_detail_info, 'd');
        $d_data = json_decode($d_data_str, true);
        $list = data_get($d_data, 'List');
        $message = "ORDER_NO(".$order_no.")".PHP_EOL."EC_ORDER_NO(".$ec_order_no.")".PHP_EOL;
        foreach ($list as $data) {
            $message .= $data['SEND_STORE_NAME'].'-'.$data['RCV_STORE_NAME'].'-'.$data['RCV_STORE_ADDRESS'].' => '.$data['STATUS_D'].'('.$data['ORDER_DATE_R'] .')'. PHP_EOL;
        }
        return $message;
    }

    public function getOrderDetail($ec_order_no = '', $order_no = '')
    {                
        $uri = 'https://ecfme.famiport.com.tw/fmedcfpwebv2/index.aspx/GetOrderDetail';
        $method = 'POST';
        $params = [];
        $params['headers'] = [            
            'user-agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.114 Safari/537.36',
            'Content-Type' => 'application/json; charset=UTF-8',   
        ];
        $params['json'] =[
            'EC_ORDER_NO'     => $ec_order_no,
            'ORDER_NO'     => $order_no,
            'RCV_USER_NAME'     => null,
        ];
        return $this->curlClientService->doRequestJsonToArray($uri, $params,$method,false);
    }


    public function doInquiryOrders($cookie = '', $ec_order = '', $text = '')
    {        
        $uri = 'https://ecfme.famiport.com.tw/fmedcfpwebv2/index.aspx/InquiryOrders';
        $method = 'POST';
        $params = [];
        $params['headers'] = [            
            'User-Agent'=> 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.114 Safari/537.36',
            'Content-Type'=> 'application/json; charset=UTF-8',
            'Cookie' => $cookie
        ];
        $params['json'] =["ListEC_ORDER_NO"=>[$ec_order],"CODE"=>$text];
        return $this->curlClientService->doRequestJsonToArray($uri, $params,$method,false);
    }

    public function doImageToText($img_url = '')
    {        
        $uri = 'https://www.prepostseo.com/frontend/extractImgText';
        $method = 'POST';
        $params = [];
        $params['headers'] = [            
            'user-agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.114 Safari/537.36',
            'accept' => 'application/json, text/javascript, */*; q=0.01',
            'x-requested-with' => 'XMLHttpRequest',
            'origin' => 'https://www.prepostseo.com',
            'referer' => 'https://www.prepostseo.com/image-to-text',
        ];
        $params['form_params'] =[
                'submit'     => true,
                'imgUrl'     => $img_url,
        ];
        return $this->curlClientService->doRequestJsonToArray($uri, $params,$method,false);
    }

    public function uploadReverseImage($captcha_image_file_path = '')
    {
        $uri = 'https://www.prepostseo.com/frontend/uploadReverseImageFiles';
        $method = 'POST';
        $params = [];
        $params['headers'] = [            
            'user-agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.114 Safari/537.36',
            'accept' => 'application/json, text/javascript, */*; q=0.01',
            'x-requested-with' => 'XMLHttpRequest',
        ];
        $params['multipart'] =[
            [
                'name'     => 'file',
                'contents' => fopen($captcha_image_file_path, 'r'),
            ]
        ];
        return $this->curlClientService->doRequestJsonToArray($uri, $params,$method,false);
    }
    public function saveCaptchaImage()
    {
        $uri = 'https://ecfme.famiport.com.tw/fmedcfpwebv2/CodeHandler.ashx';
        $method = 'GET';
        $params = [];
        $params['headers'] = [
                'user-agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.114 Safari/537.36',
        ];
        return $this->curlClientService->doRequestBase($uri, $params,$method,false);
    }
}
