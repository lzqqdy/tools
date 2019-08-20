<?php

namespace lzqqdy\tools;

/**
 * 数组处理
 * Class Arr
 * @package lzqqdy\tools
 */
class Arr
{
    /**
     * 多维数组按照某字段排序
     *
     * @param array $array 数据源
     * @param string $field 字段
     * @param string $sort 排序规则
     * $sort eg：SORT_ASC,SORT_DESC,不加引号
     * @return mixed
     */
    public static function sortArrByOneField(&$array, $field, $sort)
    {
        $fieldArr = [];
        foreach ($array as $k => $v) {
            $fieldArr[$k] = $v[$field];
        }
        array_multisort($fieldArr, $sort, $array);
        return $array;
    }

    /**
     * 二维数组根据键值去重
     *
     * @param array $arr 数据源
     * @param string $key 键值
     *
     * @return array
     */
    public static function unique(&$arr, $key)
    {
        $rAr = [];
        for ($i = 0; $i < count($arr); $i++) {
            if (!isset($rAr[$arr[$i][$key]])) {
                $rAr[$arr[$i][$key]] = $arr[$i];
            }
        }
        $arr = array_values($rAr);
        return $arr;
    }

    /**
     * 获取指定键所有值的数组
     *
     * @param array $arr 数据源
     * @param string $col 要查询的键
     *
     * @return array
     */
    public static function getCols($arr, $col)
    {
        $ret = [];
        foreach ($arr as $row) {
            if (is_array($row)) {
                if (isset($row[$col])) {
                    $ret[] = $row[$col];
                }
            } else {
                $ret = [$col => $arr[$col]];
            }
        }
        return $ret;
    }

    /**
     * 将一个二维数组按照指定字段的值分组
     *
     * @param array $arr 数据源
     * @param string $keyField 作为分组依据的键名
     *
     * @return array 分组后的结果
     */
    public static function groupBy($arr, $keyField)
    {
        $ret = [];
        foreach ($arr as $row) {
            $key = $row[$keyField];
            $ret[$key][] = $row;
        }
        return $ret;
    }

    /**
     * 从数组中删除空白的元素（包括只有空白字符的元素）
     *
     * @param array $arr 要处理的数组
     * @param boolean $trim 是否对数组元素调用 trim 函数
     */
    public static function removeEmpty(& $arr, $trim = true)
    {
        foreach ($arr as $key => $value) {
            if (is_array($value)) {
                self::removeEmpty($arr[$key]);
            } else {
                $value = trim($value); //移除字符串两侧的字符
                if ($value == '') {
                    unset($arr[$key]);
                } elseif ($trim) {
                    $arr[$key] = $value;
                }
            }
        }
    }

    /**
     * 返回数组层数(一维，二维..)
     *
     * @param array $arr 数据源
     *
     * @return int 数组层数
     */
    public static function getArrayLevel($arr)
    {
        if (!is_array($arr)) {
            return 0;
        } else {
            $max = 0;
            foreach ($arr as $v) {
                $ret = self::getArrayLevel($v);
                if ($ret > $max) {
                    $max = $ret;
                }
            }
            return $max + 1;
        }
    }

    /**
     * 二维数组根据多个字段排序
     *
     * 参数($arr, 'gender', SORT_DESC, 'age', SORT_ASC);
     * @return mixed|null
     */
    public static function sortArrByManyField()
    {
        $args = func_get_args();
        if (empty($args)) {
            return null;
        }
        $arr = array_shift($args);
        foreach ($args as $key => $value) {
            if (is_string($value)) {
                $temp = [];
                foreach ($arr as $k => $v) {
                    $temp[$k] = $v[$value];
                }
                $args[$key] = $temp;
            }
        }
        $args[] = &$arr;
        call_user_func_array('array_multisort', $args);
        return array_pop($args);
    }

    /**
     * 获取二维数组中的某一列
     *
     * @param array $data 数组
     * @param string $key 列名
     * @return array  返回那一列的数组
     */
    public static function get_arr_column($data, $key)
    {
        $arr = [];
        foreach ($data as $k => $val) {
            $arr[] = $val[$key];
        }
        return $arr;
    }

    /**
     * 多维数组转化为一维数组
     *
     * @param array $data 多维数组
     * @return array 一维数组
     */
    public static function array_multi2single($data)
    {
        $arr = [];
        foreach ($data as $value) {
            if (is_array($value)) {
                self::array_multi2single($value);
            } else {
                $arr [] = $value;
            }
        }
        return $arr;
    }

    /**
     * 将一维数组解析成键值相同的数组
     * @param $arr
     * @return array
     */
    public static function parseArr($arr)
    {
        $result = [];
        foreach ($arr as $item) {
            $result[$item] = $item;
        }
        return $result;
    }

    /**
     * 多维数组格式化日期
     *
     * @param array $data 数据源
     * @param string $field 字段
     * @param string $format
     *
     * @return array
     */
    public static function formatDate($data, $field, $format = 'y-m-d H:i')
    {
        $return = [];
        foreach ($data as $key => $value) {
            if (is_array($value)) {
                self::formatDate($value, $field);
            } else {
                if ($key == $field) {
                    $data[$key] = date($format, $value);
                    array_push($return, $data);
                }
            }
        }
        return $return ? $return : $data;
    }

    /**
     * 一维数组转二维
     * @param $array
     * @param bool $recursive
     * @param string $key
     * @param string $value
     * @return array
     */
    public static function toMapping($array, $recursive = false, $key = 'name', $value = 'value')
    {
        foreach ($array as $index => $obj) {
            $array[$index] = [
                $key   => $index,
                $value => is_array($obj) && $recursive
                    ? static::toMapping($obj, $recursive) : $obj,
            ];
        }
        return array_values($array);
    }

    /**
     * 判断二维数组是否存在某键值对
     * @param array $arr
     * @param string $key
     * @param string $value
     * @return bool
     */
    public static function if_array($arr, $key, $value)
    {
        foreach ($arr as $val) {
            if ($val[$key] == $value) {
                return true;
            }
        }
        return false;
    }

    /**
     * 数组转xml
     *
     * @param $arr
     * @return string
     */
    public static function arrayToXml($arr)
    {
        $xml = "<xml>";
        foreach ($arr as $key => $val) {
            if (is_numeric($val)) {
                $xml .= "<" . $key . ">" . $val . "</" . $key . ">";

            } else {
                $xml .= "<" . $key . "><![CDATA[" . $val . "]]></" . $key . ">";
            }
        }
        $xml .= "</xml>";
        return $xml;
    }

    /**
     * 将二维数组以指定的key作为数组的键名
     *
     * @param $arr
     * @param $key_name
     * @return array
     */
    public static function convert_arr_key($arr, $key_name)
    {
        $data = array();
        foreach ($arr as $key => $val) {
            $data[$val[$key_name]] = $val;
        }
        return $data;
    }

    /**
     * 两个数组的笛卡尔积
     *
     * @param array $arr1
     * @param array $arr2
     */
    public static function combineArray($arr1, $arr2)
    {
        $result = array();
        foreach ($arr1 as $item1) {
            foreach ($arr2 as $item2) {
                $temp = $item1;
                $temp[] = $item2;
                $result[] = $temp;
            }
        }
        return $result;
    }

    /**
     * 数组随机抽出一个
     * @param array $data
     * @return mixed|null
     */
    public static function arrayRandOne(array $data)
    {
        if (empty($data)) {
            return null;
        }
        mt_srand();
        return $data[array_rand($data)];
    }
}