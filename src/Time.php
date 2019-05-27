<?php

namespace lzqqdy\tools;

/**
 * 时间处理
 * Class Time
 * @package lzqqdy\tools
 */
class Time
{
    const YEAR = 31536000;
    const MONTH = 2592000;
    const WEEK = 604800;
    const DAY = 86400;
    const HOUR = 3600;
    const MINUTE = 60;

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
        foreach ($f as $k => $v) {
            if (0 != $c = floor($t / (int)$k)) {
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
    public static function get_weeks($time = '', $format = 'Y-m-d')
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
     * 计算两个时区间相差的时长,单位为秒
     *
     * $seconds = self::offset('America/Chicago', 'GMT');
     *
     * [!!] A list of time zones that PHP supports can be found at
     * <http://php.net/timezones>.
     *
     * @param   string $remote timezone that to find the offset of
     * @param   string $local timezone used as the baseline
     * @param   mixed $now UNIX timestamp or date string
     * @return  integer
     */
    public static function offset($remote, $local = null, $now = null)
    {
        if ($local === null) {
            // Use the default timezone
            $local = date_default_timezone_get();
        }
        if (is_int($now)) {
            // Convert the timestamp into a string
            $now = date(DateTime::RFC2822, $now);
        }
        // Create timezone objects
        $zone_remote = new DateTimeZone($remote);
        $zone_local = new DateTimeZone($local);
        // Create date objects from timezones
        $time_remote = new DateTime($now, $zone_remote);
        $time_local = new DateTime($now, $zone_local);
        // Find the offset
        $offset = $zone_remote->getOffset($time_remote) - $zone_local->getOffset($time_local);
        return $offset;
    }

    /**
     * 计算两个时间戳之间相差的时间
     *
     * $span = self::span(60, 182, 'minutes,seconds'); // array('minutes' => 2, 'seconds' => 2)
     * $span = self::span(60, 182, 'minutes'); // 2
     *
     * @param   int $remote timestamp to find the span of
     * @param   int $local timestamp to use as the baseline
     * @param   string $output formatting string
     * @return  string   when only a single output is requested
     * @return  array    associative list of all outputs requested
     * @from https://github.com/kohana/ohanzee-helpers/blob/master/src/Date.php
     */
    public static function span($remote, $local = null, $output = 'years,months,weeks,days,hours,minutes,seconds')
    {
        // Normalize output
        $output = trim(strtolower((string)$output));
        if (!$output) {
            // Invalid output
            return false;
        }
        // Array with the output formats
        $output = preg_split('/[^a-z]+/', $output);
        // Convert the list of outputs to an associative array
        $output = array_combine($output, array_fill(0, count($output), 0));
        // Make the output values into keys
        extract(array_flip($output), EXTR_SKIP);
        if ($local === null) {
            // Calculate the span from the current time
            $local = time();
        }
        // Calculate timespan (seconds)
        $timespan = abs($remote - $local);
        if (isset($output['years'])) {
            $timespan -= self::YEAR * ($output['years'] = (int)floor($timespan / self::YEAR));
        }
        if (isset($output['months'])) {
            $timespan -= self::MONTH * ($output['months'] = (int)floor($timespan / self::MONTH));
        }
        if (isset($output['weeks'])) {
            $timespan -= self::WEEK * ($output['weeks'] = (int)floor($timespan / self::WEEK));
        }
        if (isset($output['days'])) {
            $timespan -= self::DAY * ($output['days'] = (int)floor($timespan / self::DAY));
        }
        if (isset($output['hours'])) {
            $timespan -= self::HOUR * ($output['hours'] = (int)floor($timespan / self::HOUR));
        }
        if (isset($output['minutes'])) {
            $timespan -= self::MINUTE * ($output['minutes'] = (int)floor($timespan / self::MINUTE));
        }
        // Seconds ago, 1
        if (isset($output['seconds'])) {
            $output['seconds'] = $timespan;
        }
        if (count($output) === 1) {
            // Only a single output was requested, return it
            return array_pop($output);
        }
        // Return array
        return $output;
    }
}