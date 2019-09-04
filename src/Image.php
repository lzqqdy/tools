<?php
/**
 * Created by PhpStorm.
 * User: lzqqdy
 * Date: 2019/9/4
 * Time: 10:55
 */

namespace lzqqdy\tools;

/**
 * 图片处理
 * Class Image
 * @package lzqqdy\tools
 */
class Image
{
    /**
     * 图片转Base64
     * @param string $filename
     * @return string
     */
    public static function imageToBase64($filename = '')
    {
        $base64 = '';
        if (file_exists($filename)) {
            if ($fp = fopen($filename, "rb", 0)) {
                $img = fread($fp, filesize($filename));
                fclose($fp);
                $base64 = 'data:image/jpg/png/gif;base64,' . chunk_split(base64_encode($img));
            }
        }
        return $base64;
    }

    /**
     * Base64生成图片文件,自动解析格式
     * @param $base64
     * @param $path
     * @param $filename
     * @return array
     */
    public static function base64ToImage($base64, $path, $filename)
    {
        $res = array();
        //匹配base64字符串格式
        if (preg_match('/^(data:\s*image\/(\w+);base64,)/', $base64, $result)) {
            //保存最终的图片格式
            $postfix = $result[2];
            $base64 = base64_decode(substr(strstr($base64, ','), 1));
            $filename .= '.' . $postfix;
            $path .= $filename;
            //创建图片
            if (file_put_contents($path, $base64)) {
                $res['status'] = 1;
                $res['filename'] = $filename;
            } else {
                $res['status'] = 2;
                $res['err'] = 'Create img failed!';
            }
        } else {
            $res['status'] = 2;
            $res['err'] = 'Not base64 char!';
        }
        return $res;
    }
}