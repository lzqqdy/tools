<?php

namespace lzqqdy\tools;

/**
 * 常用表单验证规则
 * Class Validate
 * @package lzqqdy\tools
 */
class Validate
{

    /**
     * 用户名
     * 用户名支持中文、字母、数字、下划线，但必须以中文或字母开头，长度3-20个字符
     * @param $str
     * @return false|int
     */
    public static function check_name($str)
    {
        return preg_match("/^[\x80-\xffA-Za-z]{1,1}[\x80-\xff_A-Za-z0-9]{2,19}+$/", $str);
    }

    /**
     * Email
     * @param $str
     * @return false|int
     */
    public static function check_email($str)
    {
        return preg_match("/^([a-zA-Z0-9_\.\-])+\@(([a-zA-Z0-9\-])+\.)+([a-zA-Z0-9]{2,4})+$/", $str);
    }

    /**
     * 手机号
     * @param $num
     * @return false|int
     */
    public static function check_mobile($num)
    {
        return preg_match("/^1(3|4|5|6|7|8|9)\d{9}$/", $num);
    }
    //TODO 身份证号|密码强度|文件格式|URL|IP|邮编

}