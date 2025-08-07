<?php

require_once 'testDataFileIterator.php';

class DateTest extends PHPUnit_Framework_TestCase
{
    protected function setUp()
    {
        if (! defined('PHPEXCEL_ROOT')) {
            define('PHPEXCEL_ROOT', APPLICATION_PATH.'/');
        }
        require_once PHPEXCEL_ROOT.'PHPExcel/Autoloader.php';
    }

    public function test_set_excel_calendar()
    {
        $calendarValues = [
            PHPExcel_Shared_Date::CALENDAR_MAC_1904,
            PHPExcel_Shared_Date::CALENDAR_WINDOWS_1900,
        ];

        foreach ($calendarValues as $calendarValue) {
            $result = call_user_func(['PHPExcel_Shared_Date', 'setExcelCalendar'], $calendarValue);
            $this->assertTrue($result);
        }
    }

    public function test_set_excel_calendar_with_invalid_value()
    {
        $unsupportedCalendar = '2012';
        $result = call_user_func(['PHPExcel_Shared_Date', 'setExcelCalendar'], $unsupportedCalendar);
        $this->assertFalse($result);
    }

    /**
     * @dataProvider providerDateTimeExcelToPHP1900
     */
    public function test_date_time_excel_to_ph_p1900()
    {
        $result = call_user_func(
            ['PHPExcel_Shared_Date', 'setExcelCalendar'],
            PHPExcel_Shared_Date::CALENDAR_WINDOWS_1900
        );

        $args = func_get_args();
        $expectedResult = array_pop($args);
        if ($args[0] < 1) {
            $expectedResult += gmmktime(0, 0, 0);
        }
        $result = call_user_func_array(['PHPExcel_Shared_Date', 'ExcelToPHP'], $args);
        $this->assertEquals($expectedResult, $result);
    }

    public function providerDateTimeExcelToPHP1900()
    {
        return new testDataFileIterator('rawTestData/Shared/DateTimeExcelToPHP1900.data');
    }

    /**
     * @dataProvider providerDateTimePHPToExcel1900
     */
    public function test_date_time_php_to_excel1900()
    {
        $result = call_user_func(
            ['PHPExcel_Shared_Date', 'setExcelCalendar'],
            PHPExcel_Shared_Date::CALENDAR_WINDOWS_1900
        );

        $args = func_get_args();
        $expectedResult = array_pop($args);
        $result = call_user_func_array(['PHPExcel_Shared_Date', 'PHPToExcel'], $args);
        $this->assertEquals($expectedResult, $result, null, 1E-5);
    }

    public function providerDateTimePHPToExcel1900()
    {
        return new testDataFileIterator('rawTestData/Shared/DateTimePHPToExcel1900.data');
    }

    /**
     * @dataProvider providerDateTimeFormattedPHPToExcel1900
     */
    public function test_date_time_formatted_php_to_excel1900()
    {
        $result = call_user_func(
            ['PHPExcel_Shared_Date', 'setExcelCalendar'],
            PHPExcel_Shared_Date::CALENDAR_WINDOWS_1900
        );

        $args = func_get_args();
        $expectedResult = array_pop($args);
        $result = call_user_func_array(['PHPExcel_Shared_Date', 'FormattedPHPToExcel'], $args);
        $this->assertEquals($expectedResult, $result, null, 1E-5);
    }

    public function providerDateTimeFormattedPHPToExcel1900()
    {
        return new testDataFileIterator('rawTestData/Shared/DateTimeFormattedPHPToExcel1900.data');
    }

    /**
     * @dataProvider providerDateTimeExcelToPHP1904
     */
    public function test_date_time_excel_to_ph_p1904()
    {
        $result = call_user_func(
            ['PHPExcel_Shared_Date', 'setExcelCalendar'],
            PHPExcel_Shared_Date::CALENDAR_MAC_1904
        );

        $args = func_get_args();
        $expectedResult = array_pop($args);
        if ($args[0] < 1) {
            $expectedResult += gmmktime(0, 0, 0);
        }
        $result = call_user_func_array(['PHPExcel_Shared_Date', 'ExcelToPHP'], $args);
        $this->assertEquals($expectedResult, $result);
    }

    public function providerDateTimeExcelToPHP1904()
    {
        return new testDataFileIterator('rawTestData/Shared/DateTimeExcelToPHP1904.data');
    }

    /**
     * @dataProvider providerDateTimePHPToExcel1904
     */
    public function test_date_time_php_to_excel1904()
    {
        $result = call_user_func(
            ['PHPExcel_Shared_Date', 'setExcelCalendar'],
            PHPExcel_Shared_Date::CALENDAR_MAC_1904
        );

        $args = func_get_args();
        $expectedResult = array_pop($args);
        $result = call_user_func_array(['PHPExcel_Shared_Date', 'PHPToExcel'], $args);
        $this->assertEquals($expectedResult, $result, null, 1E-5);
    }

    public function providerDateTimePHPToExcel1904()
    {
        return new testDataFileIterator('rawTestData/Shared/DateTimePHPToExcel1904.data');
    }

    /**
     * @dataProvider providerIsDateTimeFormatCode
     */
    public function test_is_date_time_format_code()
    {
        $args = func_get_args();
        $expectedResult = array_pop($args);
        $result = call_user_func_array(['PHPExcel_Shared_Date', 'isDateTimeFormatCode'], $args);
        $this->assertEquals($expectedResult, $result);
    }

    public function providerIsDateTimeFormatCode()
    {
        return new testDataFileIterator('rawTestData/Shared/DateTimeFormatCodes.data');
    }

    /**
     * @dataProvider providerDateTimeExcelToPHP1900Timezone
     */
    public function test_date_time_excel_to_ph_p1900_timezone()
    {
        $result = call_user_func(
            ['PHPExcel_Shared_Date', 'setExcelCalendar'],
            PHPExcel_Shared_Date::CALENDAR_WINDOWS_1900
        );

        $args = func_get_args();
        $expectedResult = array_pop($args);
        if ($args[0] < 1) {
            $expectedResult += gmmktime(0, 0, 0);
        }
        $result = call_user_func_array(['PHPExcel_Shared_Date', 'ExcelToPHP'], $args);
        $this->assertEquals($expectedResult, $result);
    }

    public function providerDateTimeExcelToPHP1900Timezone()
    {
        return new testDataFileIterator('rawTestData/Shared/DateTimeExcelToPHP1900Timezone.data');
    }
}
