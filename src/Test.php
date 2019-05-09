<?php

namespace lzqqdy\tools;
/**
 * Class Test
 * @package lzqqdy\tools
 */

use lzqqdy\tools\WeChat;

class Test
{
    /**
     * @param $param
     * @return mixed
     */
    public function get($param)
    {
        return $param;
    }

    /**
     * @param $param
     * @return mixed
     */
    public function change($param)
    {
        return $param;
    }

    /**
     * WeChat使用demo
     */
    public function WxDemo()
    {
        $config = [        //小程序配置
            'appid'     => 'wx446bb***5c456b42',
            'appsecret' => '008e8073************c315d21',
        ];
        $WeChat = new WeChat($config);
        $code = '033GlfVT1pPX25198PTT1EgvVT1GlfVJ';
        $login = $WeChat->wxLogin($code);  //登录
        $token = $WeChat->getAccessToken(); //获取token
        $path = 'pages/index/index'; //小程序页面路径
        $data = [       //参数
            'path'  => $path . '?key=' . 1,
            'width' => 430,
        ];
        $ret = $WeChat->getQRCode($token, $data); //生成二维码
        if ($ret)
        {
            $WeChat->writeImg($ret, 'img', 'test.png'); //二维码写入本地
//             var_dump($ret);
        } else
        {
            var_dump($WeChat->getError()); //获取错误信息
        }
    }
}