<?php

require_once 'testDataFileIterator.php';

class NumberFormatTest extends PHPUnit_Framework_TestCase
{
    protected function setUp()
    {
        if (! defined('PHPEXCEL_ROOT')) {
            define('PHPEXCEL_ROOT', APPLICATION_PATH.'/');
        }
        require_once PHPEXCEL_ROOT.'PHPExcel/Autoloader.php';

        PHPExcel_Shared_String::setDecimalSeparator('.');
        PHPExcel_Shared_String::setThousandsSeparator(',');
    }

    /**
     * @dataProvider providerNumberFormat
     */
    public function test_format_value_with_mask()
    {
        $args = func_get_args();
        $expectedResult = array_pop($args);
        $result = call_user_func_array(['PHPExcel_Style_NumberFormat', 'toFormattedString'], $args);
        $this->assertEquals($expectedResult, $result);
    }

    public function providerNumberFormat()
    {
        return new testDataFileIterator('rawTestData/Style/NumberFormat.data');
    }
}
