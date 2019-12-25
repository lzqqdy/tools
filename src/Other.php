<?php

namespace lzqqdy\tools;

/**
 * 其他函数
 * Class Other
 * @package lzqqdy\tools
 */
class Other
{
    /**
     * 由经纬度算距离
     *
     * @param $lat1
     * @param $lng1
     * @param $lat2
     * @param $lng2
     *
     * @return float
     */
    public static function getDistance($lat1, $lng1, $lat2, $lng2)
    {
        $earthRadius = 6367000; //地球半径
        $lat1 = ($lat1 * pi()) / 180;
        $lng1 = ($lng1 * pi()) / 180;
        $lat2 = ($lat2 * pi()) / 180;
        $lng2 = ($lng2 * pi()) / 180;
        $calcLongitude = $lng2 - $lng1;
        $calcLatitude = $lat2 - $lat1;
        $stepOne = pow(sin($calcLatitude / 2), 2) + cos($lat1) * cos($lat2) * pow(sin($calcLongitude / 2), 2);
        $stepTwo = 2 * asin(min(1, sqrt($stepOne)));
        $calculatedDistance = $earthRadius * $stepTwo;
        return round($calculatedDistance);
    }

    /**
     * 获取用户IP地址
     * @return mixed
     */
    public static function getIp()
    {
        if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
            $ip = $_SERVER['HTTP_CLIENT_IP'];
        } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
        } else {
            $ip = $_SERVER['REMOTE_ADDR'];
        }
        return $ip;
    }

    /**
     * 获取服务器ip地址
     * @return string
     */
    public static function serverIP()
    {
        return gethostbyname($_SERVER["SERVER_NAME"]);
    }

    /**
     * 返回打印数组结构
     * @param string $var 数组
     * @param string $indent 缩进字符
     * @return string
     */
    public static function var_export_short($var, $indent = "")
    {
        switch (gettype($var)) {
            case "string":
                return '"' . addcslashes($var, "\\\$\"\r\n\t\v\f") . '"';
            case "array":
                $indexed = array_keys($var) === range(0, count($var) - 1);
                $r = [];
                foreach ($var as $key => $value) {
                    $r[] = "$indent    "
                        . ($indexed ? "" : self::var_export_short($key) . " => ")
                        . self::var_export_short($value, "$indent    ");
                }
                return "[\n" . implode(",\n", $r) . "\n" . $indent . "]";
            case "boolean":
                return $var ? "TRUE" : "FALSE";
            default:
                return var_export($var, true);
        }
    }

    /**
     * 密码加密
     * @param $str
     * @param string $auth_key
     * @return string
     */
    public static function pwd_md5($str, $auth_key = '')
    {
        return '' === $str ? '' : md5(sha1($str) . $auth_key);
    }

    /**
     * 发个红包
     * @desc 通过一个总金额和总个数，生成不同的红包金额，可用于微信发放红包
     * @param $total [你要发的红包总额]
     * @param int $num [发几个]，默认为10个
     * @return array [生成红包金额数组]
     */
    public static function getRedGift($total, $num = 10)
    {
        $min = 0.01;
        $temp = array();
        $return = array();
        for ($i = 1; $i < $num; ++$i) {
            $safe_total = ($total - ($num - $i) * $min) / ($num - $i); //红包金额的最大值
            if ($safe_total < 0) {
                break;
            }
            $money = @mt_rand($min * 100, $safe_total * 100) / 100;//随机产生一个红包金额
            $total = $total - $money;// 剩余红包总额
            $temp[$i] = round($money, 2);//保留两位有效数字
        }
        $temp[$i] = round($total, 2);
        $return['money_sum'] = $temp;
        $return['new_total'] = array_sum($temp);
        return $return;
    }

    /**
     * 格式化银行卡号，前四位和最后显示原样的，其他隐藏
     * @param $cardNo
     * @return string
     */
    public static function bankCardNoFormat($cardNo)
    {
        $n = strlen($cardNo);
        //判断尾部几位显示原型
        if ($n % 4 == 0) {
            $j = 4;
        } else {
            $j = $n % 4;
        }
        $str = "";
        for ($i = 0; $i < $n; $i++) {
            if ($i < 4 || $i > $n - $j - 1) {
                $str .= $cardNo[$i];
            } else {
                $str .= "*";
            }
            if ($i % 4 == 3) {
                $str .= " ";
            }
        }
        return $str;
    }

    /**
     * 格式化数据化手机号码
     * @param $mobile
     * @return string
     */
    public static function format_mobile($mobile)
    {
        return substr($mobile, 0, 3) . "****" . substr($mobile, 9, 2);
    }

    /**
     * 替换富文本编辑器中的图片地址
     * @param null $content
     * @param null $strUrl
     * @return string|string[]|null
     */
    public static function replacePicUrl($content = null, $strUrl = null)
    {
        if ($strUrl) {
            //提取图片路径的src的正则表达式 并把结果存入$matches中
            preg_match_all("/<[img|IMG].*?src=[\'|\"](.*?(?:[\.jpg|\.jpeg|\.png|\.gif|\.bmp]))[\'|\"].*?[\/]?>/",
                $content,
                $matches);

            if (!empty($matches)) {
                //注意，上面的正则表达式说明src的值是放在数组的第二个中
                $img = $matches[1];
            } else {
                $img = "";
            }
            if (!empty($img)) {

                $patterns = array();
                $replacements = array();
                foreach ($img as $imgItem) {
                    if (!filter_var($imgItem, FILTER_VALIDATE_URL, FILTER_FLAG_PATH_REQUIRED)) {
                        $final_imgUrl = $strUrl . $imgItem;
                    } else {
                        $final_imgUrl = $imgItem;
                    }
                    $replacements[] = $final_imgUrl;
                    $img_new = "/" . preg_replace("/\//i", "\/", $imgItem) . "/";
                    $patterns[] = $img_new;
                }
                //让数组按照key来排序
                ksort($patterns);
                ksort($replacements);
                //去重
                //替换内容
                $vote_content = preg_replace(array_unique($patterns), array_unique($replacements), $content);
                return $vote_content;
            } else {
                return $content;
            }
        } else {
            return $content;
        }
    }

    /**
     * 不足时几位数时，前面补零
     * @param int $len
     * @param int $number
     * @return string
     */
    public static function fillZero($len = 0, $number = 0)
    {
        return sprintf("%0" . $len . "d", $number);//生成4位数，不足前面补0
    }

    /**
     * 将数字转换成逗号分隔的形式
     * 同：number_format
     * @param $number
     * @return string
     */
    public static function number2thousands($number)
    {
        $number = (string)strrev($number);           // 反转字符串
        $number = chunk_split($number, 3, ',');      // 以逗号,将字符串分隔成小段，每小段长度为3
        $number = strrev($number);                   // 再转回来

        return ltrim($number, ',');           // 删除左边多余的逗号
    }

    /**
     * 检测时间格式是否合法
     * @param $date
     * @param string $format
     * @return bool
     */
    public static function verifyDate($date, $format = 'Y-m-d H:i:s')
    {
        return \DateTime::createFromFormat($format, $date) !== false;
    }
}