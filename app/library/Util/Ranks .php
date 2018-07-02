<?php
/**
 * Created by PhpStorm.
 * User: chinwe.jing
 * Date: 2018/7/2
 * Time: 11:46
 */

namespace Util;

use Phalcon\Di as di;
use Carbon\Carbon;

/**
 * Class Ranks
 * @package Util
 * 参考网址：https://segmentfault.com/a/1190000002694239
 */
class Ranks
{
    const PREFIX = 'rank:';
    protected $redis = null;

    /**
     * @Describe: 初始化redis
     * @Author: chinwe.jing
     * @Data: 2018/7/2 11:53
     */
    public function initialize()
    {
        $di = di::getDefault();
        $this->redis = $di->get('redis');
    }


    public function addScores($member, $scores) {
        $key = self::PREFIX . date('Ymd');
        return $this->redis->zIncrBy($key, $scores, $member);
    }


    protected function getOneDayRankings($date, $start, $stop) {
        $key = self::PREFIX . $date;
        return $this->redis->zRevRange($key, $start, $stop, true);
    }


    protected function getMultiDaysRankings($dates, $outKey, $start, $stop) {
        $keys = array_map(function($date) {
            return self::PREFIX . $date;
        }, $dates);

        $weights = array_fill(0, count($keys), 1);
        $this->redis->zUnion($outKey, $keys, $weights);
        return $this->redis->zRevRange($outKey, $start, $stop, true);
    }


    public function getYesterdayTop10() {
        $date = Carbon::now()->subDays(1)->format('Ymd');
        return $this->getOneDayRankings($date, 0, 9);
    }


    public static function getCurrentMonthDates() {
        $dt = Carbon::now();
        $days = $dt->daysInMonth;

        $dates = array();
        for ($day = 1; $day <= $days; $day++) {
            $dt->day = $day;
            $dates[] = $dt->format('Ymd');
        }
        return $dates;
    }


    public function getCurrentMonthTop10() {
        $dates = self::getCurrentMonthDates();
        return $this->getMultiDaysRankings($dates, 'rank:current_month', 0, 9);
    }
}