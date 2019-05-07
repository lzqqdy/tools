<?php

namespace lzqqdy\tools;
/**
 * 时间处理
 * Class Time
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

    /**
     * 获取本周所有日期
     * @param string $time
     * @param string $format
     * @return array
     */
    public static function get_week($time = '', $format = 'Y-m-d')
    {
        $time = $time != '' ? $time : time();
        //获取当前周几
        $week = date('w', $time);
        $date = [];
        for ($i = 1; $i <= 7; $i++)
        {
            $date[$i] = date($format, strtotime('+' . $i - $week . ' days', $time));
        }
        return $date;
    }

    /**
     * 获取最近七天所有日期
     * @param string $time
     * @param string $format
     * @return array
     */
    public static function get_weeks($time = '', $format = 'Y-m-d')
    {
        $time = $time != '' ? $time : time();
        //组合数据
        $date = [];
        for ($i = 1; $i <= 7; $i++)
        {
            $date[$i] = date($format, strtotime('+' . $i - 7 . ' days', $time));
        }
        return $date;
    }
}