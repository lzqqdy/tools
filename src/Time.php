<?php

namespace lzqqdy\tools;
/**
 * 时间处理
 * Class Str
 * @package lzqqdy\tools
 */
class Time
{
    /**
     * php将时间处理成“xx时间前”
     *
     * @param $time
     *
     * @return string
     */
    public static function formatTime($time)
    {
        $t = time() - $time;
        $f = [
            '31536000' => '年',
            '2592000'  => '个月',
            '604800'   => '星期',
            '86400'    => '天',
            '3600'     => '小时',
            '60'       => '分钟',
            '1'        => '秒',
        ];
        foreach ($f as $k => $v)
        {
            if (0 != $c = floor($t / (int)$k))
            {
                return $c . $v . '前';
            }
        }
    }
}