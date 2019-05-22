<?php

namespace lzqqdy\tools;
/**
 * 字符串处理
 * Class Str
 * @package lzqqdy\tools
 */
class Str
{
    /**
     * 将字符串分割为数组
     *
     * @param $str
     *
     * @return array[]|false|string[]
     */
    public static function mb_str_split($str)
    {
        return preg_split('/(?<!^)(?!$)/u', $str);
    }

    /**
     * 按符号截取字符串的指定部分
     *
     * @param string $str 需要截取的字符串
     * @param string $sign 需要截取的符号
     * @param int $number 如是正数以0为起点从左向右截  负数则从右向左截
     *
     * @return string 返回截取的内容
     */
    /*
      示例
      $str='123/456/789';
      cut_str($str,'/',0);  返回 123
      cut_str($str,'/',-1);  返回 789
      cut_str($str,'/',-2);  返回 456
      具体参考 http://baijunyao.com/article/18
    */
    public static function cut_str($str, $sign, $number)
    {
        $array = explode($sign, $str);
        $length = count($array);
        if ($number < 0)
        {
            $new_array = array_reverse($array);
            $abs_number = abs($number);
            if ($abs_number > $length)
            {
                return 'error';
            } else
            {
                return $new_array[$abs_number - 1];
            }
        } else
        {
            if ($number >= $length)
            {
                return 'error';
            } else
            {
                return $array[$number];
            }
        }
    }

    /**
     * 格式化字节
     *
     * @param $size string Bytes
     * @return string
     */
    public static function formatBytes($size)
    {
        if ($size >= 1073741824)
        {
            $size = round($size / 1073741824 * 100) / 100 . 'GB';
        } elseif ($size >= 1048576)
        {
            $size = round($size / 1048576 * 100) / 100 . 'MB';
        } elseif ($size >= 1024)
        {
            $size = round($size / 1024 * 100) / 100 . 'KB';
        } else
        {
            $size = $size . 'Bytes';
        }
        return $size;
    }

    /**
     * 中文字串截取(无乱码)
     *
     * @param $string string 需要截取的字符串
     * @param $start int 开始
     * @param $length int 截取长度
     * @return string string 截取后字符串
     */
    public static function getSubstr($string, $start, $length)
    {
        if (mb_strlen($string, 'utf-8') > $length)
        {
            $str = mb_substr($string, $start, $length, 'utf-8');
            return $str . '...';
        } else
        {
            return $string;
        }
    }

    /**
     * 获取url 中的各个参数  类似于pay_code=alipay&bank_code=ICBC-DEBIT
     * @param $str string $str
     * @return array
     */
    public static function parse_url_param($str)
    {
        $data = [];
        $str = explode('?', $str);
        $str = end($str);
        $parameter = explode('&', $str);
        foreach ($parameter as $val)
        {
            $tmp = explode('=', $val);
            $data[$tmp[0]] = $tmp[1];
        }
        return $data;
    }

    /**
     * 获取中文字符拼音首字母
     *
     * @param $str
     * @return string|null
     */
    public static function getFirstCharter($str)
    {
        if (empty($str))
        {
            return '';
        }
        $fchar = ord($str{0});
        if ($fchar >= ord('A') && $fchar <= ord('z')) return strtoupper($str{0});
        $s1 = iconv('UTF-8', 'gb2312//TRANSLIT//IGNORE', $str);
        $s2 = iconv('gb2312', 'UTF-8//TRANSLIT//IGNORE', $s1);
        $s = $s2 == $str ? $s1 : $str;
        $asc = ord($s{0}) * 256 + ord($s{1}) - 65536;
        if ($asc >= -20319 && $asc <= -20284) return 'A';
        if ($asc >= -20283 && $asc <= -19776) return 'B';
        if ($asc >= -19775 && $asc <= -19219) return 'C';
        if ($asc >= -19218 && $asc <= -18711) return 'D';
        if ($asc >= -18710 && $asc <= -18527) return 'E';
        if ($asc >= -18526 && $asc <= -18240) return 'F';
        if ($asc >= -18239 && $asc <= -17923) return 'G';
        if ($asc >= -17922 && $asc <= -17418) return 'H';
        if ($asc >= -17417 && $asc <= -16475) return 'J';
        if ($asc >= -16474 && $asc <= -16213) return 'K';
        if ($asc >= -16212 && $asc <= -15641) return 'L';
        if ($asc >= -15640 && $asc <= -15166) return 'M';
        if ($asc >= -15165 && $asc <= -14923) return 'N';
        if ($asc >= -14922 && $asc <= -14915) return 'O';
        if ($asc >= -14914 && $asc <= -14631) return 'P';
        if ($asc >= -14630 && $asc <= -14150) return 'Q';
        if ($asc >= -14149 && $asc <= -14091) return 'R';
        if ($asc >= -14090 && $asc <= -13319) return 'S';
        if ($asc >= -13318 && $asc <= -12839) return 'T';
        if ($asc >= -12838 && $asc <= -12557) return 'W';
        if ($asc >= -12556 && $asc <= -11848) return 'X';
        if ($asc >= -11847 && $asc <= -11056) return 'Y';
        if ($asc >= -11055 && $asc <= -10247) return 'Z';
        return null;
    }

    /**
     * 获取整条字符串汉字拼音首字母
     *
     * @param $zh
     * @return string
     */
    public static function pinyin_long($zh)
    {
        $ret = "";
        $s1 = iconv("UTF-8", "gb2312", $zh);
        $s2 = iconv("gb2312", "UTF-8", $s1);
        if ($s2 == $zh)
        {
            $zh = $s1;
        }
        for ($i = 0; $i < strlen($zh); $i++)
        {
            $s1 = substr($zh, $i, 1);
            $p = ord($s1);
            if ($p > 160)
            {
                $s2 = substr($zh, $i++, 2);
                $ret .= self::getFirstCharter($s2);
            } else
            {
                $ret .= $s1;
            }
        }
        return $ret;
    }

    /**
     * 截取字符串长度
     *
     * @param string $sourcestr 要被截取的字符串
     * @param int $cutlength 截取的长度
     * @return string
     */
    public static function cutStr($sourcestr, $cutlength)
    {
        $sourcestr = self::replaceHtml($sourcestr);
        $returnstr = "";
        $i = 0;
        $n = 0;
        $str_length = strlen($sourcestr);
        // 字符串的字节数
        while (($n < $cutlength) and ($i <= $str_length))
        {
            $temp_str = substr($sourcestr, $i, 1);
            $ascnum = Ord($temp_str);
            // 得到字符串中第$i位字符的ascii码
            // 如果ASCII位高与224，
            if ($ascnum >= 224)
            {
                $returnstr .= substr($sourcestr, $i, 3);
                // 根据UTF-8编码规范，将3个连续的字符计为单个字符
                $i = $i + 3;
                // 实际Byte计为3
                $n++; // 字串长度计1
                // 如果ASCII位高与192，
            } elseif ($ascnum >= 192)
            {
                $returnstr = $returnstr . substr($sourcestr, $i, 2);
                // 根据UTF-8编码规范，将2个连续的字符计为单个字符
                $i = $i + 2;
                // 实际Byte计为2
                $n++; // 字串长度计1
                // 如果是大写字母，
            } elseif ($ascnum >= 65 && $ascnum <= 90)
            {
                $returnstr = $returnstr . substr($sourcestr, $i, 1);
                $i = $i + 1;
                // 实际的Byte数仍计1个
                $n++; // 但考虑整体美观，大写字母计成一个高位字符
            } else
            { // 其他情况下，包括小写字母和半角标点符号，
                $returnstr = $returnstr . substr($sourcestr, $i, 1);
                $i = $i + 1;
                // 实际的Byte数计1个
                $n = $n + 0.5; // 小写字母和半角标点等与半个高位字符宽…
            }
        }
        if ($str_length > $i)
        {
            $returnstr .= "...";
            // 超过长度时在尾处加上省略号
        }
        return $returnstr;
    }

    /**
     * 去除字符串中的HTML JS CSS
     *
     * @param string $string 需要处理的字符串
     * @return string 纯文本字符串
     */
    public static function replaceHtml($string)
    {
        if (empty ($string))
        {
            return "";
        }
        $search = [
            "'<script[^>]*?>.*?</script>'si",
            "'<style[^>]*?>.*?</style>'si",
            "'<[/!]*?[^<>]*?>'si",
            "'<!--[/!]*?[^<>]*?>'si",
        ];
        $replace = ["", "", "", ""];
        $string = preg_replace($search, $replace, $string);
        return str_replace(["　", " ", "\r", "\n", "&nbsp;"], "", $string);
    }

    /**
     * 删除指定标签
     *
     * @param array $tags 删除的标签  数组形式
     * @param string $str html字符串
     * @param bool $content true保留标签的内容text
     * @return mixed
     */
    public static function strip_html_tags($tags, $str, $content = true)
    {
        $html = [];
        // 是否保留标签内的text字符
        if ($content)
        {
            foreach ($tags as $tag)
            {
                $html[] = '/(<' . $tag . '.*?>(.|\n)*?<\/' . $tag . '>)/is';
            }
        } else
        {
            foreach ($tags as $tag)
            {
                $html[] = '/(<(?:\\/' . $tag . '|' . $tag . ')[^>]*>)/is';
            }
        }
        return preg_replace($html, '', $str);
    }

    /**
     * 数字转大写（人民币）
     * 只支持11位数，9位整数+2位小数
     * @param $num
     * @return mixed
     */
    public static function numRmb($num)
    {
        $array1 = ['零', '壹', '贰', '叁', '肆', '伍', '陆', '柒', '捌', '玖'];
        $array2 = ['-1' => '角', '0' => '分', '1' => '元', '2' => '拾', '3' => '佰', '4' => '仟', '5' => '万', '6' => '', '7' => '', '8' => '', '9' => '亿'];
        # 只允许输入2位小数
        $data = explode('.', round($num, 2));
        # 先来弄整数部分
        $length = strlen($data[0]);
        $rmb = '';
        for ($i = 1; $i <= $length; $i++)
        {
            $num = $length - $i;
            $k = substr($data[0], $i - 1, 1);
            $m = substr($data[0], $i, 1);
            # 判断是不是连续的零
            $status = ($k == 0) ? true : false;
            # 处理万级以上数
            if ($num > 4)
            {
                if ($status == true)
                {
                    $rmb .= $array1[0];
                } else
                {
                    $rmb .= $array1[$k] . $array2[$num + 1];
                    # 千万级别的时候会重复出现万数
                    if ($num != 8)
                    {
                        $rmb .= $array2[$num - 3];
                    }
                    if ($num == 7 || $num == 6 || $num == 5)
                    {
                        $rmb = str_replace('万', '', $rmb);
                        $rmb .= '万';
                    }
                }
                # 处理千级以下数
            } else
            {
                if ($status == true)
                {
                    $rmb .= $array1[0];
                } else
                {
                    $rmb .= $array1[$k] . $array2[$num + 1];
                }
            }
        }
        # 再弄小数部分
        if (!empty($data[1]))
        {
            $length = strlen($data[1]);
            for ($i = 0; $i < $length; $i++)
            {
                $k = substr($data[1], $i, 1);
                $rmb .= $array1[$k] . $array2[$i - 1];
            }
        } else
        {
            $rmb .= '整';
        }
        # 要替换6个零，因为可能存在7个零的情况
        $rmb = str_replace(['零零', '零零', '零零'], '零', $rmb);
        # 兼容一些特殊情况
        return str_replace(['零角', '零零'], '零', $rmb);
    }
}