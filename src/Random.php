<?php

namespace lzqqdy\tools;
/**
 * 随机
 * Class Random
 * @package lzqqdy\tools
 */
class Random
{
    /**
     * 生成UUID
     *
     * @return string
     */
    public static function uuid()
    {
        return sprintf(
            '%04x%04x-%04x-%04x-%04x-%04x%04x%04x', mt_rand(0, 0xffff), mt_rand(0, 0xffff), mt_rand(0, 0xffff), mt_rand(0, 0x0fff) | 0x4000, mt_rand(0, 0x3fff) | 0x8000, mt_rand(0, 0xffff), mt_rand(0, 0xffff), mt_rand(0, 0xffff)
        );
    }

    /**
     * 生成不重复的随机数
     *
     * @param  int $start 需要生成的数字开始范围
     * @param  int $end 结束范围
     * @param  int $length 需要生成的随机数个数
     *
     * @return array 生成的随机数
     */
    public static function get_rand_number($start = 1, $end = 10, $length = 4)
    {
        $count = 0;
        $temp = [];
        while ($count < $length)
        {
            $temp[] = mt_rand($start, $end);
            $data = array_unique($temp);
            $count = count($data);
        }
        sort($data);
        return $data;
    }

    /**
     * 生成随机颜色
     *
     * @return string
     */
    public static function randomColor()
    {
        $str = '#';
        for ($i = 0; $i < 6; $i++)
        {
            $randNum = rand(0, 15);
            switch ($randNum)
            {
                case 10:
                    $randNum = 'A';
                    break;
                case 11:
                    $randNum = 'B';
                    break;
                case 12:
                    $randNum = 'C';
                    break;
                case 13:
                    $randNum = 'D';
                    break;
                case 14:
                    $randNum = 'E';
                    break;
                case 15:
                    $randNum = 'F';
                    break;
            }
            $str .= $randNum;
        }
        return $str;
    }
}