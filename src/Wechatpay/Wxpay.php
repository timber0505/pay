<?php

namespace Sq1994\Pay\Wechatpay;

class Wxpay
{
    protected $config = [];

    public function __construct($config = [])
    {
        $this->config = $config;
    }
}