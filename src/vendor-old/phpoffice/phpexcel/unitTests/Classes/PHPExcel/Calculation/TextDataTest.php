<?php

require_once 'testDataFileIterator.php';

class TextDataTest extends PHPUnit_Framework_TestCase
{
    protected function setUp()
    {
        if (! defined('PHPEXCEL_ROOT')) {
            define('PHPEXCEL_ROOT', APPLICATION_PATH.'/');
        }
        require_once PHPEXCEL_ROOT.'PHPExcel/Autoloader.php';

        PHPExcel_Calculation_Functions::setCompatibilityMode(PHPExcel_Calculation_Functions::COMPATIBILITY_EXCEL);
    }

    /**
     * @dataProvider providerCHAR
     */
    public function test_char()
    {
        $args = func_get_args();
        $expectedResult = array_pop($args);
        $result = call_user_func_array(['PHPExcel_Calculation_TextData', 'CHARACTER'], $args);
        $this->assertEquals($expectedResult, $result);
    }

    public function providerCHAR()
    {
        return new testDataFileIterator('rawTestData/Calculation/TextData/CHAR.data');
    }

    /**
     * @dataProvider providerCODE
     */
    public function test_code()
    {
        $args = func_get_args();
        $expectedResult = array_pop($args);
        $result = call_user_func_array(['PHPExcel_Calculation_TextData', 'ASCIICODE'], $args);
        $this->assertEquals($expectedResult, $result);
    }

    public function providerCODE()
    {
        return new testDataFileIterator('rawTestData/Calculation/TextData/CODE.data');
    }

    /**
     * @dataProvider providerCONCATENATE
     */
    public function test_concatenate()
    {
        $args = func_get_args();
        $expectedResult = array_pop($args);
        $result = call_user_func_array(['PHPExcel_Calculation_TextData', 'CONCATENATE'], $args);
        $this->assertEquals($expectedResult, $result);
    }

    public function providerCONCATENATE()
    {
        return new testDataFileIterator('rawTestData/Calculation/TextData/CONCATENATE.data');
    }

    /**
     * @dataProvider providerLEFT
     */
    public function test_left()
    {
        $args = func_get_args();
        $expectedResult = array_pop($args);
        $result = call_user_func_array(['PHPExcel_Calculation_TextData', 'LEFT'], $args);
        $this->assertEquals($expectedResult, $result);
    }

    public function providerLEFT()
    {
        return new testDataFileIterator('rawTestData/Calculation/TextData/LEFT.data');
    }

    /**
     * @dataProvider providerMID
     */
    public function test_mid()
    {
        $args = func_get_args();
        $expectedResult = array_pop($args);
        $result = call_user_func_array(['PHPExcel_Calculation_TextData', 'MID'], $args);
        $this->assertEquals($expectedResult, $result);
    }

    public function providerMID()
    {
        return new testDataFileIterator('rawTestData/Calculation/TextData/MID.data');
    }

    /**
     * @dataProvider providerRIGHT
     */
    public function test_right()
    {
        $args = func_get_args();
        $expectedResult = array_pop($args);
        $result = call_user_func_array(['PHPExcel_Calculation_TextData', 'RIGHT'], $args);
        $this->assertEquals($expectedResult, $result);
    }

    public function providerRIGHT()
    {
        return new testDataFileIterator('rawTestData/Calculation/TextData/RIGHT.data');
    }

    /**
     * @dataProvider providerLOWER
     */
    public function test_lower()
    {
        $args = func_get_args();
        $expectedResult = array_pop($args);
        $result = call_user_func_array(['PHPExcel_Calculation_TextData', 'LOWERCASE'], $args);
        $this->assertEquals($expectedResult, $result);
    }

    public function providerLOWER()
    {
        return new testDataFileIterator('rawTestData/Calculation/TextData/LOWER.data');
    }

    /**
     * @dataProvider providerUPPER
     */
    public function test_upper()
    {
        $args = func_get_args();
        $expectedResult = array_pop($args);
        $result = call_user_func_array(['PHPExcel_Calculation_TextData', 'UPPERCASE'], $args);
        $this->assertEquals($expectedResult, $result);
    }

    public function providerUPPER()
    {
        return new testDataFileIterator('rawTestData/Calculation/TextData/UPPER.data');
    }

    /**
     * @dataProvider providerPROPER
     */
    public function test_proper()
    {
        $args = func_get_args();
        $expectedResult = array_pop($args);
        $result = call_user_func_array(['PHPExcel_Calculation_TextData', 'PROPERCASE'], $args);
        $this->assertEquals($expectedResult, $result);
    }

    public function providerPROPER()
    {
        return new testDataFileIterator('rawTestData/Calculation/TextData/PROPER.data');
    }

    /**
     * @dataProvider providerLEN
     */
    public function test_len()
    {
        $args = func_get_args();
        $expectedResult = array_pop($args);
        $result = call_user_func_array(['PHPExcel_Calculation_TextData', 'STRINGLENGTH'], $args);
        $this->assertEquals($expectedResult, $result);
    }

    public function providerLEN()
    {
        return new testDataFileIterator('rawTestData/Calculation/TextData/LEN.data');
    }

    /**
     * @dataProvider providerSEARCH
     */
    public function test_search()
    {
        $args = func_get_args();
        $expectedResult = array_pop($args);
        $result = call_user_func_array(['PHPExcel_Calculation_TextData', 'SEARCHINSENSITIVE'], $args);
        $this->assertEquals($expectedResult, $result);
    }

    public function providerSEARCH()
    {
        return new testDataFileIterator('rawTestData/Calculation/TextData/SEARCH.data');
    }

    /**
     * @dataProvider providerFIND
     */
    public function test_find()
    {
        $args = func_get_args();
        $expectedResult = array_pop($args);
        $result = call_user_func_array(['PHPExcel_Calculation_TextData', 'SEARCHSENSITIVE'], $args);
        $this->assertEquals($expectedResult, $result);
    }

    public function providerFIND()
    {
        return new testDataFileIterator('rawTestData/Calculation/TextData/FIND.data');
    }

    /**
     * @dataProvider providerREPLACE
     */
    public function test_replace()
    {
        $args = func_get_args();
        $expectedResult = array_pop($args);
        $result = call_user_func_array(['PHPExcel_Calculation_TextData', 'REPLACE'], $args);
        $this->assertEquals($expectedResult, $result);
    }

    public function providerREPLACE()
    {
        return new testDataFileIterator('rawTestData/Calculation/TextData/REPLACE.data');
    }

    /**
     * @dataProvider providerSUBSTITUTE
     */
    public function test_substitute()
    {
        $args = func_get_args();
        $expectedResult = array_pop($args);
        $result = call_user_func_array(['PHPExcel_Calculation_TextData', 'SUBSTITUTE'], $args);
        $this->assertEquals($expectedResult, $result);
    }

    public function providerSUBSTITUTE()
    {
        return new testDataFileIterator('rawTestData/Calculation/TextData/SUBSTITUTE.data');
    }

    /**
     * @dataProvider providerTRIM
     */
    public function test_trim()
    {
        $args = func_get_args();
        $expectedResult = array_pop($args);
        $result = call_user_func_array(['PHPExcel_Calculation_TextData', 'TRIMSPACES'], $args);
        $this->assertEquals($expectedResult, $result);
    }

    public function providerTRIM()
    {
        return new testDataFileIterator('rawTestData/Calculation/TextData/TRIM.data');
    }

    /**
     * @dataProvider providerCLEAN
     */
    public function test_clean()
    {
        $args = func_get_args();
        $expectedResult = array_pop($args);
        $result = call_user_func_array(['PHPExcel_Calculation_TextData', 'TRIMNONPRINTABLE'], $args);
        $this->assertEquals($expectedResult, $result);
    }

    public function providerCLEAN()
    {
        return new testDataFileIterator('rawTestData/Calculation/TextData/CLEAN.data');
    }

    /**
     * @dataProvider providerDOLLAR
     */
    public function test_dollar()
    {
        $args = func_get_args();
        $expectedResult = array_pop($args);
        $result = call_user_func_array(['PHPExcel_Calculation_TextData', 'DOLLAR'], $args);
        $this->assertEquals($expectedResult, $result);
    }

    public function providerDOLLAR()
    {
        return new testDataFileIterator('rawTestData/Calculation/TextData/DOLLAR.data');
    }

    /**
     * @dataProvider providerFIXED
     */
    public function test_fixed()
    {
        $args = func_get_args();
        $expectedResult = array_pop($args);
        $result = call_user_func_array(['PHPExcel_Calculation_TextData', 'FIXEDFORMAT'], $args);
        $this->assertEquals($expectedResult, $result);
    }

    public function providerFIXED()
    {
        return new testDataFileIterator('rawTestData/Calculation/TextData/FIXED.data');
    }

    /**
     * @dataProvider providerT
     */
    public function test_t()
    {
        $args = func_get_args();
        $expectedResult = array_pop($args);
        $result = call_user_func_array(['PHPExcel_Calculation_TextData', 'RETURNSTRING'], $args);
        $this->assertEquals($expectedResult, $result);
    }

    public function providerT()
    {
        return new testDataFileIterator('rawTestData/Calculation/TextData/T.data');
    }

    /**
     * @dataProvider providerTEXT
     */
    public function test_text()
    {
        //	Enforce decimal and thousands separator values to UK/US, and currency code to USD
        call_user_func(['PHPExcel_Shared_String', 'setDecimalSeparator'], '.');
        call_user_func(['PHPExcel_Shared_String', 'setThousandsSeparator'], ',');
        call_user_func(['PHPExcel_Shared_String', 'setCurrencyCode'], '$');

        $args = func_get_args();
        $expectedResult = array_pop($args);
        $result = call_user_func_array(['PHPExcel_Calculation_TextData', 'TEXTFORMAT'], $args);
        $this->assertEquals($expectedResult, $result);
    }

    public function providerTEXT()
    {
        return new testDataFileIterator('rawTestData/Calculation/TextData/TEXT.data');
    }

    /**
     * @dataProvider providerVALUE
     */
    public function test_value()
    {
        call_user_func(['PHPExcel_Shared_String', 'setDecimalSeparator'], '.');
        call_user_func(['PHPExcel_Shared_String', 'setThousandsSeparator'], ' ');
        call_user_func(['PHPExcel_Shared_String', 'setCurrencyCode'], '$');

        $args = func_get_args();
        $expectedResult = array_pop($args);
        $result = call_user_func_array(['PHPExcel_Calculation_TextData', 'VALUE'], $args);
        $this->assertEquals($expectedResult, $result, null, 1E-8);
    }

    public function providerVALUE()
    {
        return new testDataFileIterator('rawTestData/Calculation/TextData/VALUE.data');
    }
}
