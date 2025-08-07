<?php

class TimeZoneTest extends PHPUnit_Framework_TestCase
{
    protected function setUp()
    {
        if (! defined('PHPEXCEL_ROOT')) {
            define('PHPEXCEL_ROOT', APPLICATION_PATH.'/');
        }
        require_once PHPEXCEL_ROOT.'PHPExcel/Autoloader.php';
    }

    public function test_set_timezone()
    {
        $timezoneValues = [
            'Europe/Prague',
            'Asia/Tokyo',
            'America/Indiana/Indianapolis',
            'Pacific/Honolulu',
            'Atlantic/St_Helena',
        ];

        foreach ($timezoneValues as $timezoneValue) {
            $result = call_user_func(['PHPExcel_Shared_TimeZone', 'setTimezone'], $timezoneValue);
            $this->assertTrue($result);
        }

    }

    public function test_set_timezone_with_invalid_value()
    {
        $unsupportedTimezone = 'Etc/GMT+10';
        $result = call_user_func(['PHPExcel_Shared_TimeZone', 'setTimezone'], $unsupportedTimezone);
        $this->assertFalse($result);
    }
}
