<?php

namespace Sq1994\Pay\Utils;

class Alisign
{
    public static function buildSignstring(array $body = [])
    {
        ksort($body);

        $params = "";

        foreach ($body as $k => $v) {
            $params .= $k . "=" . $v . "&";
        }
        $signtemstring = rtrim($params,"&");
        return $signtemstring;
    }

    // 支付宝签名
    public static function getSign(array $body,$privateKey)
    {
        try {
            $signtemstring = self::buildSignstring($body);

            $signtrue = "";

            $privateKey = chunk_split($privateKey, 64, "\n");

            $privateKey = "-----BEGIN RSA PRIVATE KEY-----\n$privateKey-----END RSA PRIVATE KEY-----\n";

            $private_id = openssl_get_privatekey($privateKey);

            openssl_sign($signtemstring,$signtrue,$private_id,OPENSSL_ALGO_SHA256);

            openssl_free_key($private_id);

            $signtrue = base64_encode($signtrue);

            return $signtrue;

        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }

    }

    // 回调签名认证
    public static function notifyCheckSign($notifyData,$publicKey)
    {
        try {
            $sign = $notifyData['sign'];

            unset($notifyData['sign']);

            $publicKey        = chunk_split($publicKey, 64, "\n");

            $publicKey = "-----BEGIN PUBLIC KEY-----\n$publicKey-----END PUBLIC KEY-----\n";

            $publickeysourece = openssl_get_publickey($publicKey);

            // 验证签名
            $signature = base64_decode($sign);

            $signVerified = openssl_verify(self::buildSignstring($notifyData), $signature, $publickeysourece, OPENSSL_ALGO_SHA256);

            if ($sign == $signVerified) {
                return true;
            } else {
                return false;
            }
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }
    }
}