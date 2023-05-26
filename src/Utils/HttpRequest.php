<?php

namespace Sq1994\Pay\Utils;

class HttpRequest
{
    public static function request_curl($url, $params, $method = "get", $header = ['Content-Type: application/json',], $gzip = false, $httpcode = false)
    {
        if (!$url) return;
        $ch = curl_init();
        if ($method == 'get') {
            $paramsUrl = http_build_query($params);
            if (strpos($url, '?')) {
                $url .= '&' . $paramsUrl;
            } else {
                $url .= '?' . $paramsUrl;
            }
        } elseif ($method == 'getp') {
            $paramsUrl = http_build_query($params);
            if (strpos($url, '?')) {
                $url .= '&' . $paramsUrl;
            } else {
                $url .= '?' . $paramsUrl;
            }
            curl_setopt($ch, CURLOPT_POST, 1);
        } else {
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $params);
        }
        if ($header) {
            curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
        }
        if ($gzip) {
            curl_setopt($ch, CURLOPT_ENCODING, "gzip");
        }
//    if ($header['cookie'] && $header['referer']) {
//        curl_setopt($ch, CURLOPT_COOKIE, $header['cookie']);
//        curl_setopt($ch, CURLOPT_REFERER, $header['referer']);
//    }
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);

        $cEncode = curl_exec($ch);
        if ($httpcode) {
            $cEncode = [
                'cEncode' => $cEncode,
                'httpCode' => curl_getinfo($ch, CURLINFO_HTTP_CODE)
            ];
        }
        curl_close($ch);
        return $cEncode;

    }
}