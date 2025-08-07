<?php

require_once 'testDataFileIterator.php';

class StringTest extends PHPUnit_Framework_TestCase
{
    protected function setUp()
    {
        if (! defined('PHPEXCEL_ROOT')) {
            define('PHPEXCEL_ROOT', APPLICATION_PATH.'/');
        }
        require_once PHPEXCEL_ROOT.'PHPExcel/Autoloader.php';
    }

    public function test_get_is_mb_string_enabled()
    {
        $result = call_user_func(['PHPExcel_Shared_String', 'getIsMbstringEnabled']);
        $this->assertTrue($result);
    }

    public function test_get_is_iconv_enabled()
    {
        $result = call_user_func(['PHPExcel_Shared_String', 'getIsIconvEnabled']);
        $this->assertTrue($result);
    }

    public function test_get_decimal_separator()
    {
        $localeconv = localeconv();

        $expectedResult = (! empty($localeconv['decimal_point'])) ? $localeconv['decimal_point'] : ',';
        $result = call_user_func(['PHPExcel_Shared_String', 'getDecimalSeparator']);
        $this->assertEquals($expectedResult, $result);
    }

    public function test_set_decimal_separator()
    {
        $expectedResult = ',';
        $result = call_user_func(['PHPExcel_Shared_String', 'setDecimalSeparator'], $expectedResult);

        $result = call_user_func(['PHPExcel_Shared_String', 'getDecimalSeparator']);
        $this->assertEquals($expectedResult, $result);
    }

    public function test_get_thousands_separator()
    {
        $localeconv = localeconv();

        $expectedResult = (! empty($localeconv['thousands_sep'])) ? $localeconv['thousands_sep'] : ',';
        $result = call_user_func(['PHPExcel_Shared_String', 'getThousandsSeparator']);
        $this->assertEquals($expectedResult, $result);
    }

    public function test_set_thousands_separator()
    {
        $expectedResult = ' ';
        $result = call_user_func(['PHPExcel_Shared_String', 'setThousandsSeparator'], $expectedResult);

        $result = call_user_func(['PHPExcel_Shared_String', 'getThousandsSeparator']);
        $this->assertEquals($expectedResult, $result);
    }

    public function test_get_currency_code()
    {
        $localeconv = localeconv();

        $expectedResult = (! empty($localeconv['currency_symbol'])) ? $localeconv['currency_symbol'] : '$';
        $result = call_user_func(['PHPExcel_Shared_String', 'getCurrencyCode']);
        $this->assertEquals($expectedResult, $result);
    }

    public function test_set_currency_code()
    {
        $expectedResult = '£';
        $result = call_user_func(['PHPExcel_Shared_String', 'setCurrencyCode'], $expectedResult);

        $result = call_user_func(['PHPExcel_Shared_String', 'getCurrencyCode']);
        $this->assertEquals($expectedResult, $result);
    }
}
