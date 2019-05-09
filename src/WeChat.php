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
            $result = json_decode($result, true);
            if (!$result || !empty($result['errcode']))
            {
                $errCode = $result['errcode'];
                $errMsg = $result['errmsg'];
                $this->setError(compact('errCode', 'errMsg'));
                return false;
            }
            return $result;
        }
        return false;
    }

    /**
     * 获取AccessToken
     * @param $is_save bool 是否保存到本地,不保存则不判断是否过期
     * @param $path string 保存路径
     * @param $filename string 文件名
     * @return bool
     */
    public function getAccessToken($is_save = false, $path = './', $filename = 'access_token.json')
    {
        if ($is_save === false)
        {
            $result = $this->getToken(); //获取token
            if (!$result)
            {
                $this->getError();
            }
            return $result;
        } else
        {
            $isExpires = self::isExpires($path, $filename);
            if ($isExpires === false)
            {
                $result = $this->getToken(); //获取token
                if ($result)
                {
                    $result['time'] = time();
                    file_put_contents($path . $filename, json_encode($result));
                } else
                {
                    $this->getError();
                }
                return $result;
            } else
            {
                return $isExpires;
            }
        }
    }

    /**
     * 获取token
     * @return bool|mixed
     */
    private function getToken()
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
                $this->access_token = $json;
                return $this->access_token;
            }
        }
        return false;
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
    private function getWxQRCode($token = '', $url = '', $param = [])
    {
        $data = ['access_token' => $token];
        $data = array_merge($data, $param);
        // 获取数据
        $url = $url . 'access_token=' . $token;
        $result = Http::post($url, json_encode($data));
        if ($this->json_validate($result))
        {
            $result = json_decode($result, true);
            return $result;
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
        $files = $dir . $name;
        $file = fopen($files, "w");
        fwrite($file, $string);
        fclose($file);
    }

    /**
     * 判断AccessToken过期
     * @param $path
     * @param $filename
     * @return bool|mixed
     */
    private function isExpires($path, $filename)
    {
        if (!file_exists($path . $filename))
        {
            return false;
        }
        $res = file_get_contents($path . $filename);
        $arr = json_decode($res, true);
        if ($arr && time() < (intval($arr['time']) + intval($arr['expires_in'])))
        {
            //未过期
            return $arr;
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
    private function json_validate($string)
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
     * @return array|bool
     */
    public function getError()
    {
        return $this->error ? $this->error : false;
    }
}