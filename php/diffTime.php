<?php

/**
 * 时间相关函数.
 */



class Time
{
    const FormatDateWithTime = 'Y-m-d H:i:s';

    const FormatDate = 'Y-m-d';

    const FormatUsDate = 'm/d/Y';
    /**
     * 计算两个日期之间的月份和天数差
     * @param $date1
     * @param $date2
     * @return array
     */
    public static function diffDate($dateTime1, $dateTime2)
    {
        if ($dateTime1 === $dateTime2) {
            return array('year' => 0, 'month' => 0, 'day' => 0);
        } else {
            if (strtotime($dateTime1) > strtotime($dateTime2)) {
                $tmp = $dateTime2;
                $dateTime2 = $dateTime1;
                $dateTime1 = $tmp;
            }
            $date1 = explode(" ", $dateTime1)[0];
            $date2 = explode(" ", $dateTime2)[0];
            list($Y1, $m1, $d1) = explode('-', $date1);
            list($Y2, $m2, $d2) = explode('-', $date2);

            $Y = $Y2 - $Y1;
            $m = $m2 - $m1;
            //判断两个时间是否是月的最后一天,,
            if (self::getMonthLastDay($date1) == $d1 && self::getMonthLastDay($date2) == $d2) {
                $d = 0;
            } else {
                $d = $d2 - $d1;
            }

            $time1 = explode(" ", $dateTime1)[1];
            $time2 = explode(" ", $dateTime2)[1];
            list($H1, $i1, $s1) = explode(':', $time1);
            list($H2, $i2, $s2) = explode(':', $time2);

            if ($H1 < $H2 || ($H1 == $H2 && $i1 < $i2)) {
                $d++;
            }

            if ($d < 0) {
                $d += (int)date('t', strtotime("-1 month $date2"));
                $m--;

            }
            if ($m < 0) {
                $m += 12;
                $Y--;
            }


            return array('year' => $Y, 'month' => $m, 'day' => $d);
        }

    }

    /**
     * 获取这个日期当月的最后一天
     * @param $date
     * @return false|string
     */
    public static function getMonthLastDay($date)
    {
        $firstday = date('Y-m-01', strtotime($date));
        return date('d', strtotime("$firstday +1 month -1 day"));
    }

    /**
     * 增加月份 对月份临界值处理
     * @param $date
     * @param $month
     * @return false|string
     */

    public static function addMonths($date, $month)
    {
        $dateArray = explode(" ", $date);
        $dateDay = $dateArray[0];
        if (count($dateArray) == 2) {
            $dateTime = $dateArray[1];
        } else {
            $dateTime = "00:00:00";
        }
        list($Y, $m, $d) = explode('-', $dateDay);
        list($H, $i, $s) = explode(':', $dateTime);
        if (($m + $month) > 12) {
            $Y += floor(($m + $month) / 12);
            $newM = ($m + $month) % 12;
        } else {
            $newM = $m + $month;
        }
        $lastDay = $d;
        if (self::getMonthLastDay($dateDay) == $d) {//说明是最后一天
            $newTime = mktime($H, $i, $s, $m, 5, $Y);
            //获取下个月最后一天
            $lastDay = self::getMonthLastDay(date('Y-m-d', $newTime));
        }
        $newDate = date("Y-m-d H:i:s", mktime($H, $i, $s, $newM, $lastDay, $Y));
        return $newDate;
    }

    /**
     * 减少月份,对月份临界值处理
     * @param $date
     * @param $month
     * @return false|string
     */
    public static function offMonths($date, $month)
    {
        $dateArray = explode(" ", $date);
        $dateDay = $dateArray[0];
        if (count($dateArray) == 2) {
            $dateTime = $dateArray[1];
        } else {
            $dateTime = "00:00:00";
        }

        list($Y, $m, $d) = explode('-', $dateDay);
        list($H, $i, $s) = explode(':', $dateTime);
        if (($m - $month) <= 0) {
            $year = ceil(($m - $month) / 12);
            $Y = $Y - $year;
            $newM = $m + $year * 12 - $month;
        } else {
            $newM = $m - $month;
        }
        $lastDay = $d;
        if (self::getMonthLastDay($dateDay) == $d) {//说明是最后一天
            $newTime = mktime($H, $i, $s, $m, 1, $Y);
            //获取下个月最后一天
            $lastDay = self::getMonthLastDay(date('Y-m-d', $newTime));
        }
        $newDate = date("Y-m-d H:i:s", mktime($H, $i, $s, $newM, $lastDay, $Y));
        return $newDate;
    }

    /**
     * 换算之前某个时间为当前月份的时间
     * @param $date
     * @return false|string
     */
    public static function genStartTime($date)
    {
        $dateArray = explode(" ", $date);
        $dateDay = $dateArray[0];
        if (count($dateArray) == 2) {
            $dateTime = $dateArray[1];
        } else {
            $dateTime = "00:00:00";
        }
        list($Y, $m, $d) = explode('-', $dateDay);
        list($H, $i, $s) = explode(':', $dateTime);

        $nowDate = self::format(time());
        $nowDateArray = explode(" ", $nowDate);
        $nowDay = $nowDateArray[0];
        $nowTime = $nowDateArray[1];
        list($nY, $nm, $nd) = explode('-', $nowDay);
        list($nH, $ni, $ns) = explode(':', $nowTime);


        $ld = self::getMonthLastDay($date);
        $lnd = self::getMonthLastDay($nowDate);

        $startDay = $d == $ld ? $lnd : $d;
        $startTime = self::format(mktime($H, $i, $s, $nm, $startDay, $nY));
        if (($d > $nd) || ($d == $nd && mktime($H, $i, $s) > mktime($nH, $ni, $ns))) {
            $startTime = self::offMonths($startTime, 1);
        }
        return $startTime;
    }
}
