<?php

namespace lzqqdy\tools;

use lzqqdy\tools\Http;
use lzqqdy\tools\File;

/**
 * 微信小程序部分常用接口
 * Class WeChat
 * @package lzqqdy\tools
 */
class WeChat
{
    const API_URL_PREFIX = 'https://api.weixin.qq.com/cgi-bin';
    const AUTH_URL = '/token?grant_type=client_credential&';
    const AUTH_CODE2_SESSION = 'https://api.weixin.qq.com/sns/jscode2session?';
    const CREATE_WXA_QRCODE = 'https://api.weixin.qq.com/cgi-bin/wxaapp/createwxaqrcode?';
    const GET_WXA_CODE = 'https://api.weixin.qq.com/wxa/getwxacode?';

    const GRAND_TYPE = 'authorization_code';

    private $appid;
    private $appsecret;
    private $access_token;
    protected $error = [];

    public function __construct($options)
    {
        $this->appid = isset($options['appid']) ? $options['appid'] : '';
        $this->appsecret = isset($options['appsecret']) ? $options['appsecret'] : '';
    }

    /**
     * 小程序code登录
     * @param $code
     * @return bool|mixed
     */
    public function wxLogin($code)
    {
        $url = self::AUTH_CODE2_SESSION . 'appid=' . $this->appid . '&secret=' . $this->appsecret . '&js_code=' . $code . '&grant_type=' . self::GRAND_TYPE;
        $result = Http::get($url);
        if ($result)
        {
            $json = json_decode($result, true);
            if (!$json || !empty($json['errcode']))
            {
                $errCode = $json['errcode'];
                $errMsg = $json['errmsg'];
                $this->setError(compact('errCode', 'errMsg'));
                return false;
            }
            return $json;
        }
        return false;
    }

    /**
     * 获取AccessToken并保存本地
     * @return bool|array|string
     */
    public function getAccessToken()
    {
        //todo 灵活定义保存名称和路径
        $isExpires = self::isExpires();
        if ($isExpires === false)
        {
            $result = Http::get(self::API_URL_PREFIX . self::AUTH_URL . 'appid=' . $this->appid . '&secret=' . $this->appsecret);
            if ($result)
            {
                $json = json_decode($result, true);
                if (!$json || !empty($json['errcode']))
                {
                    $errCode = $json['errcode'];
                    $errMsg = $json['errmsg'];
                    $this->setError(compact('errCode', 'errMsg'));
                    return false;
                } else
                {
                    $json['time'] = time();
                    file_put_contents('./access_token.json', json_encode($json));
                    $this->access_token = $json['access_token'];
                    return $this->access_token;
                }
            }
            return false;
        } else
        {
            return $isExpires;
        }
    }

    /**
     * 获取小程序二维码
     * @param $token
     * @param $data
     * @return mixed|string
     */
    public function getQRCode($token, $data)
    {
        $result = $this->getWxQRCode($token, self::CREATE_WXA_QRCODE, $data);
        return $result;
    }

    /**
     * 获取小程序码
     * @param $token
     * @param $data
     * @return mixed|string
     */
    public function getACode($token, $data)
    {
        //接口只能生成已发布的小程序的二维码
        $result = $this->getWxQRCode($token, self::GET_WXA_CODE, $data);
        return $result;
    }

    /**
     * 获取小程序两种二维码
     * @param $token
     * @param string $url
     * @param array $param
     * @return mixed|string
     */
    protected function getWxQRCode($token = '', $url = '', $param = [])
    {
        $data = ['access_token' => $token];
        $data = array_merge($data, $param);
        // 获取数据
        $url = $url . 'access_token=' . $token;
        $result = Http::post($url, json_encode($data));
        if ($this->json_validate($result))
        {
            $json = json_decode($result, true);
            return $json;
        } else
        {
            return $result;
        }
    }

    /**
     * 将二进制图片写入文件
     * @param string $string
     * @param string $dir 文件夹名称
     * @param string $name 文件名称
     */
    public function writeImg($string, $dir, $name)
    {
        File::mk_dir($dir);
        $files = $dir . '/' . $name;
        $file = fopen($files, "w");
        fwrite($file, $string);
        fclose($file);
    }

    /**
     * 判断AccessToken过期
     * @return bool
     */
    public function isExpires()
    {
        if (!file_exists('./access_token.json'))
        {
            return false;
        }
        $res = file_get_contents('./access_token.json');
        $arr = json_decode($res, true);
        if ($arr && time() < (intval($arr['time']) + intval($arr['expires_in'])))
        {
            //未过期
            return $arr['access_token'];
        } else
        {
            return false;
        }
    }

    /**
     * json验证
     * @param $string
     * @return bool
     */
    protected function json_validate($string)
    {
        if (is_string($string))
        {
            @json_decode($string);
            return (json_last_error() === JSON_ERROR_NONE);
        }
        return false;
    }

    /**
     * 设置错误信息
     * @param $error
     * @return $this
     */
    public function setError($error)
    {
        $this->error = $error;
        return $this;
    }

    /**
     * 获取错误信息
     * @return array
     */
    public function getError()
    {
        return $this->error ? $this->error : [];
    }
}