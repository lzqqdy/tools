<?php

namespace lzqqdy\tools;
/**
 * Class Common
 * @package lzqqdy\tools
 * @describe 常用方法收集
 */
class Common
{
    /**
     * 多维数组按照某字段排序
     * @param $array
     * @param $field
     * @param $sort
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
     * @param $data
     * @param $field
     * @param string $format
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
     * 获取文件树
     * @param $dir
     * @param int $key
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
                    'id' => $id,
                    'pId' => $key,
                    'label' => $filename,
                    'isParent' => 1,
                    'path' => $path,
                ];
                $temp = $this->getFilesTree($file, $id);
                if (is_array($temp)) {
                    $files = array_merge($files, $temp);
                }
            } else {
                $files[] = [
                    'id' => $id,
                    'pId' => $key,
                    'label' => $filename,
                    'isParent' => 0,
                    'path' => $path,
                ];
            }
        }
        return $files;
    }

    /**
     * 将数据集格式化成树形层次结构
     * @param array/object $lists 要格式化的数据集，可以是数组，也可以是对象
     * @param int $pid 父级id
     * @param int $max_level 最多返回多少层，0为不限制
     * @param int $curr_level 当前层数
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
     * @param $arr
     * @param $key
     * @return array
     */
    function assoc_unique(&$arr, $key)
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
     * curl请求Api接口
     * @param $uri
     * @param array $data
     * @param string $method
     * @param null $secret
     * @param null $key
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
}