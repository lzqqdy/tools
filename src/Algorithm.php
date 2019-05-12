<?php

namespace lzqqdy\tools;
/**
 * php实现基础算法
 * Class Algorithm
 * @package lzqqdy\tools
 */
class Algorithm
{
    // +----------------------------------------------------------------------
    //冒泡排序以其像气泡一样将元素交换到顶端的形式从而命名冒泡排序
    //原理：(以升序为例) 依次比较相邻的两个数，将小数放前，大数放后，一直到第N个元素，然后重复该操作，
    //一直到第N-1个元素，循环，直到第一个元素。
    //因为每一轮比较下来，我们都会获得一个当前剩余数组的最大值，并放在了最终位置
    //最坏时间复杂度O(n^2) ： 初始数组逆序
    //最好时间复杂度O(n) ： 初始数组正序
    //平均时间复杂度O(n^2)
    // +----------------------------------------------------------------------

    /**
     * 冒泡算法1
     * @param $arr
     * @return mixed
     */
    public function bubble_sort_1($arr)
    {
        $n = count($arr);
        for ($i = 0; $i < $n - 1; $i++)
        {
            for ($j = $i + 1; $j < $n; $j++)
            {
                if ($arr[$j] < $arr[$i])
                {
                    $temp = $arr[$i];
                    $arr[$i] = $arr[$j];
                    $arr[$j] = $temp;
                }
            }
        }
        return $arr;
    }

    /**
     * 冒泡算法2
     * @param $arr
     * @return mixed
     */
    public function bubble_sort_2($arr)
    {
        $n = count($arr);
        for ($i = 0; $i < $n - 1; $i++)
        {
            for ($j = 0; $j < $n - 1 - $i; $j++)
            {
                if ($arr[$j] > $arr[$j + 1])
                {
                    list($arr[$j], $arr[$j + 1]) = [$arr[$j + 1], $arr[$j]];
                }
            }
        }
        return $arr;
    }
}
