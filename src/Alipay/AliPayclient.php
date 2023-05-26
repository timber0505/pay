<?php

namespace Sq1994\Pay\Alipay;
use Sq1994\Pay\Utils\Alisign;
use Sq1994\Pay\Utils\HttpRequest;

class AliPayclient
{
    protected $config = [];

    public function __construct($config = [])
    {
        $this->config = $config;
    }

    // 当面付
    public function facepay(string $out_trade_no,float $total_amount,string $subject,string $attach)
    {
        $url  = "https://openapi.alipay.com/gateway.do";
        if (empty($out_trade_no)) {
            throw new \Exception("out_trade_no no empty");
        }
        if (empty($total_amount)) {
            throw new \Exception("total_amount no empty");
        }
        if (empty($subject)) {
            throw new \Exception("subject no empty");
        }
        $params =  array(
            "app_id"=>$this->config['ali']['app_id'],
            "method"=>"alipay.trade.precreate",
            'charset'=>"utf-8",
            "sign_type"=>"RSA2",
            "timestamp"=>date("Y-m-d H:i:s"),
            'version'=>"1.0",
            "notify_url"=>$this->config['ali']['notify_url'],
            "biz_content"=>""
        );
        $order = [
            "out_trade_no"=>$out_trade_no,'total_amount'=>$total_amount,'subject'=>$subject,
        ];
        if (!empty($attach)) {
            $order['body'] = $attach;
        }
        $params['biz_content'] = json_encode($order);

        $privateKeyString = $this->config['ali']['privatekey'];


        $params['sign'] = Alisign::getSign($params,$privateKeyString);

        $result = HttpRequest::request_curl($url,$params,'get');
        $result = json_decode($result,true);
        if (!$result) {
            throw new \Exception("network error");
        }
        if ($result['alipay_trade_precreate_response']['code'] == 10000 && $result['alipay_trade_precreate_response']['msg'] == "Success") {
            return $result['alipay_trade_precreate_response']['qr_code'];
        } else {
            throw new \Exception($result['alipay_trade_precreate_response']['msg']);
        }
    }
}