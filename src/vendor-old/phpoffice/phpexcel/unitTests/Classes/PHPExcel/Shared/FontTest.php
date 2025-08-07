<?php

require_once 'testDataFileIterator.php';

class FontTest extends PHPUnit_Framework_TestCase
{
    protected function setUp()
    {
        if (! defined('PHPEXCEL_ROOT')) {
            define('PHPEXCEL_ROOT', APPLICATION_PATH.'/');
        }
        require_once PHPEXCEL_ROOT.'PHPExcel/Autoloader.php';
    }

    public function test_get_auto_size_method()
    {
        $expectedResult = PHPExcel_Shared_Font::AUTOSIZE_METHOD_APPROX;

        $result = call_user_func(['PHPExcel_Shared_Font', 'getAutoSizeMethod']);
        $this->assertEquals($expectedResult, $result);
    }

    public function test_set_auto_size_method()
    {
        $autosizeMethodValues = [
            PHPExcel_Shared_Font::AUTOSIZE_METHOD_EXACT,
            PHPExcel_Shared_Font::AUTOSIZE_METHOD_APPROX,
        ];

        foreach ($autosizeMethodValues as $autosizeMethodValue) {
            $result = call_user_func(['PHPExcel_Shared_Font', 'setAutoSizeMethod'], $autosizeMethodValue);
            $this->assertTrue($result);
        }
    }

    public function test_set_auto_size_method_with_invalid_value()
    {
        $unsupportedAutosizeMethod = 'guess';

        $result = call_user_func(['PHPExcel_Shared_Font', 'setAutoSizeMethod'], $unsupportedAutosizeMethod);
        $this->assertFalse($result);
    }

    /**
     * @dataProvider providerFontSizeToPixels
     */
    public function test_font_size_to_pixels()
    {
        $args = func_get_args();
        $expectedResult = array_pop($args);
        $result = call_user_func_array(['PHPExcel_Shared_Font', 'fontSizeToPixels'], $args);
        $this->assertEquals($expectedResult, $result);
    }

    public function providerFontSizeToPixels()
    {
        return new testDataFileIterator('rawTestData/Shared/FontSizeToPixels.data');
    }

    /**
     * @dataProvider providerInchSizeToPixels
     */
    public function test_inch_size_to_pixels()
    {
        $args = func_get_args();
        $expectedResult = array_pop($args);
        $result = call_user_func_array(['PHPExcel_Shared_Font', 'inchSizeToPixels'], $args);
        $this->assertEquals($expectedResult, $result);
    }

    public function providerInchSizeToPixels()
    {
        return new testDataFileIterator('rawTestData/Shared/InchSizeToPixels.data');
    }

    /**
     * @dataProvider providerCentimeterSizeToPixels
     */
    public function test_centimeter_size_to_pixels()
    {
        $args = func_get_args();
        $expectedResult = array_pop($args);
        $result = call_user_func_array(['PHPExcel_Shared_Font', 'centimeterSizeToPixels'], $args);
        $this->assertEquals($expectedResult, $result);
    }

    public function providerCentimeterSizeToPixels()
    {
        return new testDataFileIterator('rawTestData/Shared/CentimeterSizeToPixels.data');
    }
}
