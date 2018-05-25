package _go

import (
    "time"
    "fmt"
)
//换算之前某个时间为当前月份的时间
func GenStartTime(startStr string) (time.Time, error) {
    startTime, err := time.Parse("2006-01-02 15:04:05", startStr) //转化为：2006-01-02 15:04:05这个格式
    if err != nil {
	return startTime, err
    }
    now := time.Now()
    nY, nm, nd := now.Date()        //获取今天年月日的天数
    _, _, sd := startTime.Date()    //获取开始的日
    nH, ni, ns := now.Clock()       //获取今天的时分秒
    sH, si, ss := startTime.Clock() //获取开始时分秒
    startDay := sd
    ld := GetLastOfMonth(startTime)
    lnd := GetLastOfMonth(now)
    if sd == ld { //说明是月份最后一天,
	startDay = lnd
    }
    startTime = time.Date(nY, nm, startDay, sH, si, ss, 0, time.UTC)
    if (nd < sd) || (nd == sd && time.Date(2006, 01, 02, nH, ni, ns, 0, time.UTC).Before(time.Date(2006, 01, 02, sH, si, ss, 0, time.UTC))) {
	//(现在时间<开始时间）
	//如果现在时间<开始时间，开始时间减一个月
	startTime = OffMonths(startTime, 1)

    }
    return startTime, nil
}

//指定日期当月的最后一天
func GetLastOfMonth(now time.Time) int {
    currentYear, currentMonth, _ := now.Date()
    firstOfMonth := time.Date(currentYear, currentMonth, 1, 0, 0, 0, 0, time.UTC)
    lastOfMonth := firstOfMonth.AddDate(0, 1, -1)
    return lastOfMonth.Day()
}

//增加月份,处理月份天数的临界值
func AddMonths(timer time.Time, month time.Month) time.Time {
    Y, m, d := timer.Date()
    H, i, s := timer.Clock()
    newM := m
    if (m + month) > 12 {
	Y += int((m + month) / 12)
	newM = (m + month) % 12
    } else {
	newM = m + month
    }
    lastDay := d
    if GetLastOfMonth(timer) == d {
	newTime := time.Date(Y, newM, 1, 0, 0, 0, 0, time.UTC)
	lastDay = GetLastOfMonth(newTime)
    }
    return time.Date(Y, newM, lastDay, H, i, s, 0, time.UTC)
}

//减少月份处理月份天数的临界值
func OffMonths(timer time.Time, month time.Month) time.Time {
    Y, m, d := timer.Date()
    H, i, s := timer.Clock()
    newM := m
    if (m - month) <= 0 {
	year := int((m-month)/12) + 1
	Y = Y - year
	newM = m + time.Month(year*12) - month
    } else {
	newM = m - month
    }
    lastDay := d

    if GetLastOfMonth(timer) == d {
	newTime := time.Date(Y, newM, 1, 0, 0, 0, 0, time.UTC)
	lastDay = GetLastOfMonth(newTime)
    }
    return time.Date(Y, newM, lastDay, H, i, s, 0, time.UTC)
}

//计算两个日期之间的相差几年几个月几天
func DiffDate(dateOne, dateTwo time.Time) map[string]int {
    result := make(map[string]int)

    if dateOne == dateTwo {
	result["year"], result["month"], result["day"] = 0, 0, 0
	return result
    }
    if dateOne.Unix() > dateTwo.Unix() {
	tmp := dateTwo
	dateTwo = dateOne
	dateOne = tmp
    }
    fmt.Println(dateOne)
    Y1, m1, d1 := dateOne.Date()
    Y2, m2, d2 := dateTwo.Date()
    H1, i1, _ := dateOne.Clock()
    H2, i2, _ := dateTwo.Clock()
    Y := Y1 - Y2
    m := m2 - m1
    d := 0
    if GetLastOfMonth(dateOne) == d1 && GetLastOfMonth(dateTwo) == d2 {
	d = 0
    } else {
	d = d2 - d1
    }
    if H1 < H2 || (H1 == H2 && i1 < i2) {
	d++
    }
    if d < 0 {
	d += GetLastOfMonth(dateTwo)
	m--
    }

    if m < 0 {
	m += 12
	Y--
    }
    result["year"], result["month"], result["day"] = Y, int(m), d

    return result

}