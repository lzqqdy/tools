<?php

namespace lzqqdy\tools;

/**
 * Class All
 * @package lzqqdy\tools
 * @describe 方法合集
 * 具体调用按照分类文件
 */
class All
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
    function sortArrByOneField(&$array, $field, $sort)
    {
        $fieldArr = [];
        foreach ($array as $k => $v) {
            $fieldArr[$k] = $v[$field];
        }
        array_multisort($fieldArr, $sort, $array);
        return $array;
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
    function formatDate($data, $field, $format = 'y-m-d H:i')
    {
        $return = [];
        foreach ($data as $key => $value) {
            if (is_array($value)) {
                $this->formatDate($value, $field);
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
     * 遍历文件夹获取文件树
     *
     * @param string $dir 文件夹路径
     * @param int $key
     *
     * @return array|bool
     */
    function getFilesTree($dir, $key = 0)
    {
        if (!is_dir($dir)) {
            return false;
        }
        $domain = $_SERVER['SERVER_NAME'];
        $dir = $dir . "/";
        $files = [];
        $pattern = $dir . "*";
        $file_arr = glob($pattern);
        foreach ($file_arr as $k => $file) {
            $filename = str_replace('/', '', strrchr($file, '/'));
            $id = $k + 1;
            if ($key > 0) {
                $id = $id + ($key * 10);
            }
            $new_file = str_replace('./', '', $file);
            $path = $domain . '/' . $new_file;
            if (is_dir($file)) {
                $files[] = [
                    'id'       => $id,
                    'pId'      => $key,
                    'label'    => $filename,
                    'isParent' => 1,
                    'path'     => $path,
                ];
                $temp = $this->getFilesTree($file, $id);
                if (is_array($temp)) {
                    $files = array_merge($files, $temp);
                }
            } else {
                $files[] = [
                    'id'       => $id,
                    'pId'      => $key,
                    'label'    => $filename,
                    'isParent' => 0,
                    'path'     => $path,
                ];
            }
        }
        return $files;
    }

    /**
     * 将数据集格式化成树形层次结构
     *
     * @param array/object $lists 要格式化的数据集，可以是数组，也可以是对象
     * @param int $pid 父级id
     * @param int $max_level 最多返回多少层，0为不限制
     * @param int $curr_level 当前层数
     *
     * @return array
     */
    function toLayer($lists = [], $pid = 0, $max_level = 0, $curr_level = 0)
    {
        $trees = [];
        $lists = array_values($lists);
        foreach ($lists as $key => $value) {
            if ($value['pId'] == $pid) {
                if ($max_level > 0 && $curr_level == $max_level) {
                    return $trees;
                }
                unset($lists[$key]);
                $child = $this->toLayer($lists, $value['id'], $max_level, $curr_level + 1);
                if (!empty($child)) {
                    $value['children'] = $child;
                }
                $trees[] = $value;
            }
        }
        return $trees;
    }

    /**
     * 二维数组根据键值去重
     *
     * @param array $arr 数据源
     * @param string $key 键值
     *
     * @return array
     */
    function unique(&$arr, $key)
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
    function getCols($arr, $col)
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
    function groupBy($arr, $keyField)
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
    function removeEmpty(& $arr, $trim = true)
    {
        foreach ($arr as $key => $value) {
            if (is_array($value)) {
                $this->removeEmpty($arr[$key]);
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
    function getArrayLevel($arr)
    {
        if (!is_array($arr)) {
            return 0;
        } else {
            $max = 0;
            foreach ($arr as $v) {
                $ret = $this->getArrayLevel($v);
                if ($ret > $max) {
                    $max = $ret;
                }
            }
            return $max + 1;
        }
    }

    /**
     * curl请求Api接口
     *
     * @param $uri
     * @param array $data
     * @param string $method
     * @param null $secret
     * @param null $key
     *
     * @return mixed
     * @throws \Exception
     */
    function requestApi($uri, array $data = [], $method = 'get', $secret = null, $key = null)
    {
        $method = strtoupper($method);
        $ch = curl_init();
        if ('GET' == $method) {
            if ($data) {
                if (strpos($uri, '?')) {
                    foreach ($data as $key => $item) {
                        $uri .= "&{$key}={$item}";
                    }
                } else {
                    $uri .= '?' . urldecode(http_build_query($data));
                }
            }
        }
        $params[CURLOPT_URL] = $uri;
        $params[CURLOPT_RETURNTRANSFER] = 1;
        $params[CURLOPT_SSL_VERIFYPEER] = false;
        $params[CURLOPT_SSL_VERIFYHOST] = false;
        if ($method == 'POST') {
            $params[CURLOPT_POST] = 1;
            $params[CURLOPT_POSTFIELDS] = $data;
        }

        curl_setopt($ch, CURLOPT_URL, $uri);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        if ($method == 'POST') {
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, is_array($data) ? urldecode(http_build_query($data)) : $data);
        }
        if ($secret && $key) {
            $params[CURLOPT_SSLCERTTYPE] = 'PEM';
            $params[CURLOPT_SSLCERT] = $secret;
            $params[CURLOPT_SSLKEYTYPE] = 'PEM';
            $params[CURLOPT_SSLKEY] = $key;
        }
        curl_setopt_array($ch, $params);
        if (curl_errno($ch)) {
            throw new \Exception(curl_error($ch));
        }
        $response = curl_exec($ch);
        curl_close($ch);
        return $response;
    }

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
    function getDistance($lat1, $lng1, $lat2, $lng2)
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
     * php将时间处理成“xx时间前”
     *
     * @param $time
     *
     * @return string
     */
    function formatTime($time)
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
        foreach ($f as $k => $v) {
            if (0 != $c = floor($t / (int)$k)) {
                return $c . $v . '前';
            }
        }
    }

    /**
     * 二维数组根据多个字段排序
     * 参数($arr, 'gender', SORT_DESC, 'age', SORT_ASC);
     * @return mixed|null
     */
    function sortArrByManyField()
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
     * 获取用户IP地址
     * @return mixed
     */
    function getIp()
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
     * 将字符串分割为数组
     *
     * @param $str
     *
     * @return array[]|false|string[]
     */
    function mb_str_split($str)
    {
        return preg_split('/(?<!^)(?!$)/u', $str);
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
    function get_rand_number($start = 1, $end = 10, $length = 4)
    {
        $count = 0;
        $temp = [];
        while ($count < $length) {
            $temp[] = mt_rand($start, $end);
            $data = array_unique($temp);
            $count = count($data);
        }
        sort($data);
        return $data;
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
    /*  示例
        $str='123/456/789';
        cut_str($str,'/',0);  返回 123
        cut_str($str,'/',-1);  返回 789
        cut_str($str,'/',-2);  返回 456
        具体参考 http://baijunyao.com/article/18
    */
    function cut_str($str, $sign, $number)
    {
        $array = explode($sign, $str);
        $length = count($array);
        if ($number < 0) {
            $new_array = array_reverse($array);
            $abs_number = abs($number);
            if ($abs_number > $length) {
                return 'error';
            } else {
                return $new_array[$abs_number - 1];
            }
        } else {
            if ($number >= $length) {
                return 'error';
            } else {
                return $array[$number];
            }
        }
    }

    /**
     * 获取二维数组中的某一列
     * @param array $data 数组
     * @param string $key 列名
     * @return array  返回那一列的数组
     */
    function get_arr_column($data, $key)
    {
        $arr = [];
        foreach ($data as $k => $val) {
            $arr[] = $val[$key];
        }
        return $arr;
    }

    /**
     * 多维数组转化为一维数组
     * @param array $data 多维数组
     * @return array 一维数组
     */
    function array_multi2single($data)
    {
        $arr = [];
        foreach ($data as $value) {
            if (is_array($value)) {
                $this->array_multi2single($value);
            } else {
                $arr [] = $value;
            }
        }
        return $arr;
    }

    /**
     * 获取服务器ip地址
     * @return string
     */
    function serverIP()
    {
        return gethostbyname($_SERVER["SERVER_NAME"]);
    }

    /**
     * 格式化字节
     * @param $size Bytes
     * @return string
     */
    public function formatBytes($size)
    {
        if ($size >= 1073741824) {
            $size = round($size / 1073741824 * 100) / 100 . 'GB';
        } elseif ($size >= 1048576) {
            $size = round($size / 1048576 * 100) / 100 . 'MB';
        } elseif ($size >= 1024) {
            $size = round($size / 1024 * 100) / 100 . 'KB';
        } else {
            $size = $size . 'Bytes';
        }
        return $size;
    }

    /**
     * 生成随机颜色
     * @return string
     */
    function randomColor()
    {
        $str = '#';
        for ($i = 0; $i < 6; $i++) {
            $randNum = rand(0, 15);
            switch ($randNum) {
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

    /**
     * 将一维数组解析成键值相同的数组
     * @param $arr
     * @return array
     */
    function parseArr($arr)
    {
        $result = [];
        foreach ($arr as $item) {
            $result[$item] = $item;
        }
        return $result;
    }

    /**
     * 获取本周所有日期
     * @param string $time
     * @param string $format
     * @return array
     */
    function get_week($time = '', $format = 'Y-m-d')
    {
        $time = $time != '' ? $time : time();
        //获取当前周几
        $week = date('w', $time);
        $date = [];
        for ($i = 1; $i <= 7; $i++) {
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
    function get_weeks($time = '', $format = 'Y-m-d')
    {
        $time = $time != '' ? $time : time();
        //组合数据
        $date = [];
        for ($i = 1; $i <= 7; $i++) {
            $date[$i] = date($format, strtotime('+' . $i - 7 . ' days', $time));
        }
        return $date;
    }

    /**
     * 一维数组转二维
     * @param $array
     * @param bool $recursive
     * @param string $key
     * @param string $value
     * @return array
     */
    public function toMapping($array, $recursive = false, $key = 'name', $value = 'value')
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
    public function if_array($arr, $key, $value)
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
    public function arrayToXml($arr)
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
    public function convert_arr_key($arr, $key_name)
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
    public function combineArray($arr1, $arr2)
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
}
