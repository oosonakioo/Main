<?php

use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Request;
use Jenssegers\Date\Date;

class Helper
{
    public static function datetime($datetime, $format = 'd F Y h:i', $isAdmin = false)
    {
        $date = new Date($datetime);

        if ($isAdmin) {
            return $date->format($format);
        }

        if (LaravelLocalization::getCurrentLocale() == 'th' && ! $isAdmin) {
            $monthPos = strpos($format, 'M');
            if ($monthPos !== false) {
                $format = str_replace('M', 'F', $format);
            }

            $yearPos = strpos($format, 'Y');
            if ($yearPos !== false) {
                $format = str_replace('Y', '', $format);
                $thYear = intval($date->format('Y')) + 543;

                return $date->format($format).$thYear;
            } else {
                return $date->format($format);
            }
        } else {
            return $date->format($format);
        }
    }

    public static function thai_date($time, $pattern)
    {
        if (LaravelLocalization::getCurrentLocale() == 'th') {
            $thai_day_arr = ['อาทิตย์', 'จันทร์', 'อังคาร', 'พุธ', 'พฤหัสบดี', 'ศุกร์', 'เสาร์'];
            $thai_month_arr = [
                '0' => '',
                '1' => 'มกราคม',
                '2' => 'กุมภาพันธ์',
                '3' => 'มีนาคม',
                '4' => 'เมษายน',
                '5' => 'พฤษภาคม',
                '6' => 'มิถุนายน',
                '7' => 'กรกฎาคม',
                '8' => 'สิงหาคม',
                '9' => 'กันยายน',
                '10' => 'ตุลาคม',
                '11' => 'พฤศจิกายน',
                '12' => 'ธันวาคม',
            ];
        } else {
            $thai_day_arr = ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];
            $thai_month_arr = [
                '0' => '',
                '1' => 'January',
                '2' => 'February',
                '3' => 'March',
                '4' => 'April',
                '5' => 'May',
                '6' => 'June',
                '7' => 'July',
                '8' => 'August',
                '9' => 'September',
                '10' => 'October',
                '11' => 'November',
                '12' => 'December',
            ];
        }

        switch ($pattern) {
            case 'full':
                $thai_date_return = 'วัน'.$thai_day_arr[date('w', $time)];
                $thai_date_return .= 'ที่ '.date('j', $time);
                $thai_date_return .= ' เดือน'.$thai_month_arr[date('n', $time)];
                $thai_date_return .= ' พ.ศ.'.(date('Y', $time));
                $thai_date_return .= '  '.date('H:i', $time).' น.';
                break;
            case 'short':
                $thai_date_return = date('j', $time).' ';
                $thai_date_return .= $thai_month_arr[date('n', $time)].' ';
                $thai_date_return .= date('Y', $time);
                $thai_date_return .= '  '.date('H:i', $time);
                break;
            case 'onlydate':
                $thai_date_return = date('j', $time).' ';
                $thai_date_return .= $thai_month_arr[date('n', $time)].' ';
                $thai_date_return .= date('Y', $time);
                break;
            case 'onlytime':
                $thai_date_return = date('H:i', $time);
                break;
            case 'onlymonth':
                $thai_date_return = $thai_month_arr[date('n', $time)];
                break;
            case 'onlynumberday':
                $thai_date_return = date('j', $time);
                break;
        }

        return $thai_date_return;
    }

    public static function getValue($obj, $key, $errors, $lang = '')
    {
        if (empty($lang)) {
            $oldKey = $key;
            $value = $obj[$key];
        } else {
            $oldKey = $key.'_'.$lang;
            $value = $obj->translate($lang)[$key];
        }

        if (count($errors) > 0 || (empty($value) && strlen($value) == 0)) {
            return old($oldKey);
        } else {
            return $value;
        }
    }

    public static function isSelected($isSelected, $selected = 'selected')
    {
        return $isSelected ? $selected : '';
    }

    public static function isChecked($strOriginal, $strCompare)
    {
        // echo "original =". $strOriginal;
        // echo "<BR>";
        // echo "compare = ". $strCompare;
        // echo "<BR>";
        $strOriginal = ','.$strOriginal.',';
        $strCompare = ','.$strCompare.',';

        if (strpos($strOriginal, $strCompare) !== false) {
            return 'checked';
        } else {
            return '';
        }
    }

    /**
     * for localize.
     */
    public static function url($path = null, $parameters = [], $secure = null)
    {
        if (! App::isLocale(config('app.fallback_locale'))) {
            $path = App::getLocale().'/'.$path;
        }

        return app('Illuminate\Contracts\Routing\UrlGenerator')->to($path, $parameters, $secure);
    }

    public static function redirect($to = null, $status = 302, $headers = [], $secure = null)
    {
        if (! App::isLocale(config('app.fallback_locale'))) {
            $to = App::getLocale().'/'.$to;
        }

        if (is_null($to)) {
            return app('redirect');
        }

        return app('redirect')->to($to, $status, $headers, $secure);
    }

    public static function setActive($path, $active = 'active')
    {
        if (is_array($path)) {
            $segments = array_slice(Request::segments(), 1);
            $currentPath = implode('/', $segments);

            return in_array($currentPath, $path) ? $active : '';
        } else {
            $route = explode('/', $path);
            $size = count($route);
            if ($route[$size - 1] == '*') {
                return Request::is($path) || Request::is('*/'.$path) ? $active : '';
            } else {
                $segments = array_slice(Request::segments(), 1, $size);
                $segmentsLocale = array_slice(Request::segments(), 0, $size);

                return implode('/', $segments) == implode('/', $route) ? $active : ''
                || implode('/', $segmentsLocale) == implode('/', $route) ? $active : '';
            }
        }
    }

    public static function activeThis($path)
    {

        $arr_menu = $path;
        $stopcheck = false;
        $activereturn = '';
        $currenturl = $_SERVER['REQUEST_URI'];

        foreach ($arr_menu as $value) {

            // echo $value;
            // echo "<BR>";

            if (strpos($currenturl, $value) !== false) {
                $activereturn = 'active';
                $stopcheck = true;
            } else {
                if ($stopcheck) {

                } else {
                    $activereturn = '';
                }
            }
        }

        return $activereturn;
    }

    public static function isMobileDevice()
    {
        $aMobileUA = [
            '/iphone/i' => 'iPhone',
            '/ipod/i' => 'iPod',
            '/ipad/i' => 'iPad',
            '/android/i' => 'Android',
            '/blackberry/i' => 'BlackBerry',
            '/webos/i' => 'Mobile',
        ];

        // Return true if Mobile User Agent is detected
        foreach ($aMobileUA as $sMobileKey => $sMobileOS) {
            if (preg_match($sMobileKey, $_SERVER['HTTP_USER_AGENT'])) {
                return true;
            }
        }

        // Otherwise return false..
        return false;
    }

    public static function filesize_formatted($file)
    {
        $path = asset('').$file;
        $path = str_replace('\\', '/', $path);
        // echo $path;

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $path);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HEADER, true);
        curl_setopt($ch, CURLOPT_NOBODY, true);
        curl_exec($ch);
        $size = curl_getinfo($ch, CURLINFO_CONTENT_LENGTH_DOWNLOAD);

        // var_dump($size);
        // var_dump(curl_getinfo($ch));

        if ($size >= 1073741824) {
            return number_format($size / 1073741824, 2).' GB';
        } elseif ($size >= 1048576) {
            return number_format($size / 1048576, 2).' MB';
        } elseif ($size >= 1024) {
            return number_format($size / 1024, 2).' KB';
        } elseif ($size > 1) {
            return $size.' bytes';
        } elseif ($size == 1) {
            return '1 byte';
        } else {
            return '0 bytes';
        }
    }

    public static function numberFormatPrecision($number, $precision = 2, $separator = '.')
    {
        $numberParts = explode($separator, $number);
        $response = number_format($numberParts[0]);
        if (count($numberParts) > 1) {
            $response .= $separator;
            $response .= substr($numberParts[1], 0, $precision);
        }

        return $response;
    }
}
