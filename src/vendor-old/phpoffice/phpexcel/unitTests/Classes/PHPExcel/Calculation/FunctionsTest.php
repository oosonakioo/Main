<?php

require_once 'testDataFileIterator.php';

class FunctionsTest extends PHPUnit_Framework_TestCase
{
    protected function setUp()
    {
        if (! defined('PHPEXCEL_ROOT')) {
            define('PHPEXCEL_ROOT', APPLICATION_PATH.'/');
        }
        require_once PHPEXCEL_ROOT.'PHPExcel/Autoloader.php';

        PHPExcel_Calculation_Functions::setCompatibilityMode(PHPExcel_Calculation_Functions::COMPATIBILITY_EXCEL);
    }

    public function test_dummy()
    {
        $result = PHPExcel_Calculation_Functions::DUMMY();
        $this->assertEquals('#Not Yet Implemented', $result);
    }

    public function test_di_v0()
    {
        $result = PHPExcel_Calculation_Functions::DIV0();
        $this->assertEquals('#DIV/0!', $result);
    }

    public function test_na()
    {
        $result = PHPExcel_Calculation_Functions::NA();
        $this->assertEquals('#N/A', $result);
    }

    public function test_na_n()
    {
        $result = PHPExcel_Calculation_Functions::NaN();
        $this->assertEquals('#NUM!', $result);
    }

    public function test_name()
    {
        $result = PHPExcel_Calculation_Functions::NAME();
        $this->assertEquals('#NAME?', $result);
    }

    public function test_ref()
    {
        $result = PHPExcel_Calculation_Functions::REF();
        $this->assertEquals('#REF!', $result);
    }

    public function test_null()
    {
        $result = PHPExcel_Calculation_Functions::NULL();
        $this->assertEquals('#NULL!', $result);
    }

    public function test_value()
    {
        $result = PHPExcel_Calculation_Functions::VALUE();
        $this->assertEquals('#VALUE!', $result);
    }

    /**
     * @dataProvider providerIS_BLANK
     */
    public function test_i_s_blank()
    {
        $args = func_get_args();
        $expectedResult = array_pop($args);
        $result = call_user_func_array(['PHPExcel_Calculation_Functions', 'IS_BLANK'], $args);
        $this->assertEquals($expectedResult, $result, null, 1E-8);
    }

    public function providerIS_BLANK()
    {
        return new testDataFileIterator('rawTestData/Calculation/Functions/IS_BLANK.data');
    }

    /**
     * @dataProvider providerIS_ERR
     */
    public function test_i_s_err()
    {
        $args = func_get_args();
        $expectedResult = array_pop($args);
        $result = call_user_func_array(['PHPExcel_Calculation_Functions', 'IS_ERR'], $args);
        $this->assertEquals($expectedResult, $result, null, 1E-8);
    }

    public function providerIS_ERR()
    {
        return new testDataFileIterator('rawTestData/Calculation/Functions/IS_ERR.data');
    }

    /**
     * @dataProvider providerIS_ERROR
     */
    public function test_i_s_error()
    {
        $args = func_get_args();
        $expectedResult = array_pop($args);
        $result = call_user_func_array(['PHPExcel_Calculation_Functions', 'IS_ERROR'], $args);
        $this->assertEquals($expectedResult, $result, null, 1E-8);
    }

    public function providerIS_ERROR()
    {
        return new testDataFileIterator('rawTestData/Calculation/Functions/IS_ERROR.data');
    }

    /**
     * @dataProvider providerERROR_TYPE
     */
    public function test_erro_r_type()
    {
        $args = func_get_args();
        $expectedResult = array_pop($args);
        $result = call_user_func_array(['PHPExcel_Calculation_Functions', 'ERROR_TYPE'], $args);
        $this->assertEquals($expectedResult, $result, null, 1E-8);
    }

    public function providerERROR_TYPE()
    {
        return new testDataFileIterator('rawTestData/Calculation/Functions/ERROR_TYPE.data');
    }

    /**
     * @dataProvider providerIS_LOGICAL
     */
    public function test_i_s_logical()
    {
        $args = func_get_args();
        $expectedResult = array_pop($args);
        $result = call_user_func_array(['PHPExcel_Calculation_Functions', 'IS_LOGICAL'], $args);
        $this->assertEquals($expectedResult, $result, null, 1E-8);
    }

    public function providerIS_LOGICAL()
    {
        return new testDataFileIterator('rawTestData/Calculation/Functions/IS_LOGICAL.data');
    }

    /**
     * @dataProvider providerIS_NA
     */
    public function test_i_s_na()
    {
        $args = func_get_args();
        $expectedResult = array_pop($args);
        $result = call_user_func_array(['PHPExcel_Calculation_Functions', 'IS_NA'], $args);
        $this->assertEquals($expectedResult, $result, null, 1E-8);
    }

    public function providerIS_NA()
    {
        return new testDataFileIterator('rawTestData/Calculation/Functions/IS_NA.data');
    }

    /**
     * @dataProvider providerIS_NUMBER
     */
    public function test_i_s_number()
    {
        $args = func_get_args();
        $expectedResult = array_pop($args);
        $result = call_user_func_array(['PHPExcel_Calculation_Functions', 'IS_NUMBER'], $args);
        $this->assertEquals($expectedResult, $result, null, 1E-8);
    }

    public function providerIS_NUMBER()
    {
        return new testDataFileIterator('rawTestData/Calculation/Functions/IS_NUMBER.data');
    }

    /**
     * @dataProvider providerIS_TEXT
     */
    public function test_i_s_text()
    {
        $args = func_get_args();
        $expectedResult = array_pop($args);
        $result = call_user_func_array(['PHPExcel_Calculation_Functions', 'IS_TEXT'], $args);
        $this->assertEquals($expectedResult, $result, null, 1E-8);
    }

    public function providerIS_TEXT()
    {
        return new testDataFileIterator('rawTestData/Calculation/Functions/IS_TEXT.data');
    }

    /**
     * @dataProvider providerIS_NONTEXT
     */
    public function test_i_s_nontext()
    {
        $args = func_get_args();
        $expectedResult = array_pop($args);
        $result = call_user_func_array(['PHPExcel_Calculation_Functions', 'IS_NONTEXT'], $args);
        $this->assertEquals($expectedResult, $result, null, 1E-8);
    }

    public function providerIS_NONTEXT()
    {
        return new testDataFileIterator('rawTestData/Calculation/Functions/IS_NONTEXT.data');
    }

    /**
     * @dataProvider providerIS_EVEN
     */
    public function test_i_s_even()
    {
        $args = func_get_args();
        $expectedResult = array_pop($args);
        $result = call_user_func_array(['PHPExcel_Calculation_Functions', 'IS_EVEN'], $args);
        $this->assertEquals($expectedResult, $result, null, 1E-8);
    }

    public function providerIS_EVEN()
    {
        return new testDataFileIterator('rawTestData/Calculation/Functions/IS_EVEN.data');
    }

    /**
     * @dataProvider providerIS_ODD
     */
    public function test_i_s_odd()
    {
        $args = func_get_args();
        $expectedResult = array_pop($args);
        $result = call_user_func_array(['PHPExcel_Calculation_Functions', 'IS_ODD'], $args);
        $this->assertEquals($expectedResult, $result, null, 1E-8);
    }

    public function providerIS_ODD()
    {
        return new testDataFileIterator('rawTestData/Calculation/Functions/IS_ODD.data');
    }

    /**
     * @dataProvider providerTYPE
     */
    public function test_type()
    {
        $args = func_get_args();
        $expectedResult = array_pop($args);
        $result = call_user_func_array(['PHPExcel_Calculation_Functions', 'TYPE'], $args);
        $this->assertEquals($expectedResult, $result, null, 1E-8);
    }

    public function providerTYPE()
    {
        return new testDataFileIterator('rawTestData/Calculation/Functions/TYPE.data');
    }

    /**
     * @dataProvider providerN
     */
    public function test_n()
    {
        $args = func_get_args();
        $expectedResult = array_pop($args);
        $result = call_user_func_array(['PHPExcel_Calculation_Functions', 'N'], $args);
        $this->assertEquals($expectedResult, $result, null, 1E-8);
    }

    public function providerN()
    {
        return new testDataFileIterator('rawTestData/Calculation/Functions/N.data');
    }
}
