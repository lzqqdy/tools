<?php

namespace lzqqdy\tools;

/**
 * HTTP请求
 * Class Http
 *
 * @package lzqqdy\tools
 */
class Http
{

    /**
     * 发送一个POST请求
     *
     * @param       $url
     * @param array $params
     * @param array $options
     *
     * @return mixed|string
     */
    public static function post($url, $params = [], $options = [])
    {
        $req = self::sendRequest($url, $params, 'POST', $options);

        return $req['ret'] ? $req['msg'] : '';
    }

    /**
     * 发送一个GET请求
     *
     * @param       $url
     * @param array $params
     * @param array $options
     *
     * @return mixed|string
     */
    public static function get($url, $params = [], $options = [])
    {
        $req = self::sendRequest($url, $params, 'GET', $options);

        return $req['ret'] ? $req['msg'] : '';
    }

    /**
     * CURL发送Request请求,含POST和REQUEST
     *
     * @param string $url 请求的链接
     * @param mixed $params 传递的参数
     * @param string $method 请求的方法
     * @param mixed $options CURL的参数
     *
     * @return array
     */
    public static function sendRequest($url, $params = [], $method = 'POST', $options = [])
    {
        $method = strtoupper($method);
        $protocol = substr($url, 0, 5);
        $query_string = is_array($params) ? http_build_query($params) : $params;

        $ch = curl_init();
        $defaults = [];
        if ('GET' == $method) {
            $geturl = $query_string ? $url . (stripos($url,
                    "?") !== false ? "&" : "?") . $query_string : $url;
            $defaults[CURLOPT_URL] = $geturl;
        } else {
            $defaults[CURLOPT_URL] = $url;
            if ($method == 'POST') {
                $defaults[CURLOPT_POST] = 1;
            } else {
                $defaults[CURLOPT_CUSTOMREQUEST] = $method;
            }
            $defaults[CURLOPT_POSTFIELDS] = $query_string;
        }

        $defaults[CURLOPT_HEADER] = false;
        $defaults[CURLOPT_USERAGENT] = "Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/45.0.2454.98 Safari/537.36";
        $defaults[CURLOPT_FOLLOWLOCATION] = true;
        $defaults[CURLOPT_RETURNTRANSFER] = true;
        $defaults[CURLOPT_CONNECTTIMEOUT] = 3;
        $defaults[CURLOPT_TIMEOUT] = 3;

        // disable 100-continue
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Expect:'));

        if ('https' == $protocol) {
            $defaults[CURLOPT_SSL_VERIFYPEER] = false;
            $defaults[CURLOPT_SSL_VERIFYHOST] = false;
        }

        curl_setopt_array($ch, (array)$options + $defaults);

        $ret = curl_exec($ch);
        $err = curl_error($ch);

        if (false === $ret || !empty($err)) {
            $errno = curl_errno($ch);
            $info = curl_getinfo($ch);
            curl_close($ch);

            return [
                'ret'   => false,
                'errno' => $errno,
                'msg'   => $err,
                'info'  => $info,
            ];
        }
        curl_close($ch);

        return [
            'ret' => true,
            'msg' => $ret,
        ];
    }

    /**
     * 异步发送一个请求
     *
     * @param string $url 请求的链接
     * @param mixed $params 请求的参数
     * @param string $method 请求的方法
     *
     * @return boolean TRUE
     */
    public static function sendAsyncRequest($url, $params = [], $method = 'POST')
    {
        $method = strtoupper($method);
        $method = $method == 'POST' ? 'POST' : 'GET';
        //构造传递的参数
        if (is_array($params)) {
            $post_params = [];
            foreach ($params as $k => &$v) {
                if (is_array($v)) {
                    $v = implode(',', $v);
                }
                $post_params[] = $k . '=' . urlencode($v);
            }
            $post_string = implode('&', $post_params);
        } else {
            $post_string = $params;
        }
        $parts = parse_url($url);
        //构造查询的参数
        if ($method == 'GET' && $post_string) {
            $parts['query'] = isset($parts['query']) ? $parts['query'] . '&' . $post_string : $post_string;
            $post_string = '';
        }
        $parts['query'] = isset($parts['query']) && $parts['query'] ? '?' . $parts['query'] : '';
        //发送socket请求,获得连接句柄
        $fp = fsockopen($parts['host'], isset($parts['port']) ? $parts['port'] : 80, $errno, $errstr, 3);
        if (!$fp) {
            return false;
        }
        //设置超时时间
        stream_set_timeout($fp, 3);
        $out = "{$method} {$parts['path']}{$parts['query']} HTTP/1.1\r\n";
        $out .= "Host: {$parts['host']}\r\n";
        $out .= "Content-Type: application/x-www-form-urlencoded\r\n";
        $out .= "Content-Length: " . strlen($post_string) . "\r\n";
        $out .= "Connection: Close\r\n\r\n";
        if ($post_string !== '') {
            $out .= $post_string;
        }
        fwrite($fp, $out);
        //不用关心服务器返回结果
        //echo fread($fp, 1024);
        fclose($fp);

        return true;
    }

    /**
     * 发送文件到客户端
     *
     * @param string $file
     * @param bool $delaftersend
     * @param bool $exitaftersend
     */
    public static function sendToBrowser($file, $delaftersend = true, $exitaftersend = true)
    {
        if (file_exists($file) && is_readable($file)) {
            header('Content-Description: File Transfer');
            header('Content-Type: application/octet-stream');
            header('Content-Disposition: attachment;filename = ' . basename($file));
            header('Content-Transfer-Encoding: binary');
            header('Expires: 0');
            header('Cache-Control: must-revalidate, post-check = 0, pre-check = 0');
            header('Pragma: public');
            header('Content-Length: ' . filesize($file));
            ob_clean();
            flush();
            readfile($file);
            if ($delaftersend) {
                unlink($file);
            }
            if ($exitaftersend) {
                exit;
            }
        }
    }

    /**
     * 下载图片到本地
     * @param        $url
     * @param string $dir 文件夹名称
     * @param string $name 文件名称
     * @return mixed
     */
    public static function downImg($url, $dir, $name)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 30);
        $file = curl_exec($ch);
        curl_close($ch);

        $savePath = $dir . $name . '.jpg';
        $resource = fopen($savePath, 'a');
        fwrite($resource, $file);
        fclose($resource);
        return $savePath;
    }

    /**
     * @desc 异步将远程链接上的内容(图片或内容)写到本地
     * @param $url string    远程地址
     * @param $saveName string   保存在服务器上的文件名
     * @param $path string    保存路径
     * @return boolean
     */
    function putFileFromUrlContent($url, $saveName, $path)
    {
        // 设置运行时间为无限制
        set_time_limit(0);
        $url = trim($url);
        $curl = curl_init();
        // 设置你需要抓取的URL
        curl_setopt($curl, CURLOPT_URL, $url);
        // 设置header
        curl_setopt($curl, CURLOPT_HEADER, 0);
        // 设置cURL 参数，要求结果保存到字符串中还是输出到屏幕上。
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        //这个是重点，加上这个便可以支持http和https下载
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        // 运行cURL，请求网页
        $file = curl_exec($curl);
        // 关闭URL请求
        curl_close($curl);
        // 将文件写入获得的数据
        $filename = $path . $saveName;
        $write = @fopen($filename, "w");
        if ($write == false) {
            return false;
        }
        if (fwrite($write, $file) == false) {
            return false;
        }
        if (fclose($write) == false) {
            return false;
        }
        return true;
    }

    /**
     * 批量请求无依赖关系的接口【POST】
     * eg：
     * $url1= ['url' => 'www.baidu.com', 'param' => ['id'=>1,'type'=>2]];
     * $url2= ['url' => 'www.qq.com', 'param' => ['id'=>1,'type'=>2]];
     * Http::sendPostMultiRequest($url1, $url2，$url3);
     *
     * @param mixed ...$url
     * PHP > 5.6
     * @return mixed 二维数组
     */
    public static function sendPostMultiRequest(...$url)
    {
        return self::_multiCurlRequest($url);
    }

    /**
     * multi_curl并发请求多个接口
     * @param $url
     * @return bool
     */
    private static function _multiCurlRequest($url)
    {
        $mh = curl_multi_init();

        array_map(function ($item) use ($mh) {
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $item['url']);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_HEADER, 0);
            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
            curl_setopt($ch, CURLOPT_TIMEOUT, $item['timeout']);
            curl_setopt($ch, CURLOPT_USERAGENT,
                'Mozilla/5.0 (iPhone; CPU iPhone OS 12_1_1 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Mobile/16C50 renrenmine');
            if ($item['param']) {
                curl_setopt($ch, CURLOPT_POST, true);
                curl_setopt($ch, CURLOPT_POSTFIELDS, $item['param']);
            }
            curl_multi_add_handle($mh, $ch);
        }, $url);

        do {
            $mc = curl_multi_exec($mh, $runing);
            //当返回值等于CURLM_CALL_MULTI_PERFORM时，表明数据还在写入或读取中，执行循环，
            //当第一次$ch句柄的数据写入或读取成功后，返回值变为CURLM_OK，跳出本次循环，进入下面的大循环之中
        } while ($mc == CURLM_CALL_MULTI_PERFORM);

        while ($runing && $mc == CURLM_OK) {
            //阻塞直到cURL批处理连接中有活动连接。成功时返回描述符集合中描述符的数量。失败时，select失败时返回-1
            if (curl_multi_select($mh) != -1) {
                //$mh批处理中还有可执行的$ch句柄，curl_multi_select($mh) != -1程序退出阻塞状态。
                do {
                    //有活动连接时执行
                    $mc = curl_multi_exec($mh, $runing);
                } while ($mc == CURLM_CALL_MULTI_PERFORM);
            }

            while ($done = curl_multi_info_read($mh)) {
                //获取信息
                $info = curl_getinfo($done['handle']);
                $results = curl_multi_getcontent($done['handle']);

                //数据返回格式
                //url：url与结果对应，数组按照相应时间升序排序
                $output[] = ['url' => $info['url'], 'http_code' => $info['http_code'], 'results' => $results];

                curl_multi_remove_handle($mh, $done['handle']);
                curl_close($done['handle']);
            }
        }
        curl_multi_close($mh);
        return $output ? $output : false;
    }
}
