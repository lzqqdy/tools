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

    /**
     * 微信端
     * @return bool
     */
    public static function is_wechat()
    {
        return strpos($_SERVER ['HTTP_USER_AGENT'], 'MicroMessenger') !== false ? true : false;
    }

    /**
     * 判断是否SSL协议
     * @return bool
     */
    public static function is_ssl()
    {
        if (isset($_SERVER['HTTPS']) && ('1' == $_SERVER['HTTPS'] || 'on' == strtolower($_SERVER['HTTPS']))) {
            return true;
        } elseif (isset($_SERVER['SERVER_PORT']) && ('443' == $_SERVER['SERVER_PORT'])) {
            return true;
        }
        return false;
    }

    /**
     * 固话
     * @param $num
     * @return bool
     */
    public static function check_telephone($num)
    {
        if (preg_match('/^([0-9]{3,4}-)?[0-9]{7,8}$/', $num)) {
            return true;
        }
        return false;
    }
    //TODO 密码强度|文件格式|URL|IP|邮编

    /**
     * 严格校验身份证信息
     * @param $id_card
     * @return bool
     */
    public static function check_id_card($id_card)
    {
        if (strlen($id_card) == 18) {
            return self::id_card_checksum18($id_card);
        } elseif ((strlen($id_card) == 15)) {
            $id_card = self::id_card_15to18($id_card);
            return self::id_card_checksum18($id_card);
        } else {
            return false;
        }
    }

    /**
     * 将15位身份证升级到18位
     * @param $id_card
     * @return bool|string
     */
    private static function id_card_15to18($id_card)
    {
        if (strlen($id_card) != 15) {
            return false;
        } else {
            // 如果身份证顺序码是996 997 998 999，这些是为百岁以上老人的特殊编码
            if (array_search(substr($id_card, 12, 3), array('996', '997', '998', '999')) !== false) {
                $id_card = substr($id_card, 0, 6) . '18' . substr($id_card, 6, 9);
            } else {
                $id_card = substr($id_card, 0, 6) . '19' . substr($id_card, 6, 9);
            }
        }
        $id_card = $id_card . self::id_card_verify_number($id_card);
        return $id_card;
    }

    /**
     * 18位身份证校验码有效性检查
     * @param $id_card
     * @return bool
     */
    private static function id_card_checksum18($id_card)
    {
        if (strlen($id_card) != 18) {
            return false;
        }
        $id_card_base = substr($id_card, 0, 17);
        if (self::id_card_verify_number($id_card_base) != strtoupper(substr($id_card, 17, 1))) {
            return false;
        } else {
            return true;
        }
    }

    /**
     * 计算身份证校验码，根据国家标准GB 11643-1999
     * @param $id_card_base
     * @return bool|mixed
     */
    private static function id_card_verify_number($id_card_base)
    {
        if (strlen($id_card_base) != 17) {
            return false;
        }
        //加权因子
        $factor = array(7, 9, 10, 5, 8, 4, 2, 1, 6, 3, 7, 9, 10, 5, 8, 4, 2);
        //校验码对应值
        $verify_number_list = array('1', '0', 'X', '9', '8', '7', '6', '5', '4', '3', '2');
        $checksum = 0;
        for ($i = 0; $i < strlen($id_card_base); $i++) {
            $checksum += substr($id_card_base, $i, 1) * $factor[$i];
        }
        $mod = $checksum % 11;
        $verify_number = $verify_number_list[$mod];
        return $verify_number;
    }
    //github 提交测试1
}