<?php

require_once 'testDataFileIterator.php';

class MathTrigTest extends PHPUnit_Framework_TestCase
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
     * @dataProvider providerATAN2
     */
    public function test_ata_n2()
    {
        $args = func_get_args();
        $expectedResult = array_pop($args);
        $result = call_user_func_array(['PHPExcel_Calculation_MathTrig', 'ATAN2'], $args);
        $this->assertEquals($expectedResult, $result, null, 1E-12);
    }

    public function providerATAN2()
    {
        return new testDataFileIterator('rawTestData/Calculation/MathTrig/ATAN2.data');
    }

    /**
     * @dataProvider providerCEILING
     */
    public function test_ceiling()
    {
        $args = func_get_args();
        $expectedResult = array_pop($args);
        $result = call_user_func_array(['PHPExcel_Calculation_MathTrig', 'CEILING'], $args);
        $this->assertEquals($expectedResult, $result, null, 1E-12);
    }

    public function providerCEILING()
    {
        return new testDataFileIterator('rawTestData/Calculation/MathTrig/CEILING.data');
    }

    /**
     * @dataProvider providerCOMBIN
     */
    public function test_combin()
    {
        $args = func_get_args();
        $expectedResult = array_pop($args);
        $result = call_user_func_array(['PHPExcel_Calculation_MathTrig', 'COMBIN'], $args);
        $this->assertEquals($expectedResult, $result, null, 1E-12);
    }

    public function providerCOMBIN()
    {
        return new testDataFileIterator('rawTestData/Calculation/MathTrig/COMBIN.data');
    }

    /**
     * @dataProvider providerEVEN
     */
    public function test_even()
    {
        $args = func_get_args();
        $expectedResult = array_pop($args);
        $result = call_user_func_array(['PHPExcel_Calculation_MathTrig', 'EVEN'], $args);
        $this->assertEquals($expectedResult, $result, null, 1E-12);
    }

    public function providerEVEN()
    {
        return new testDataFileIterator('rawTestData/Calculation/MathTrig/EVEN.data');
    }

    /**
     * @dataProvider providerODD
     */
    public function test_odd()
    {
        $args = func_get_args();
        $expectedResult = array_pop($args);
        $result = call_user_func_array(['PHPExcel_Calculation_MathTrig', 'ODD'], $args);
        $this->assertEquals($expectedResult, $result, null, 1E-12);
    }

    public function providerODD()
    {
        return new testDataFileIterator('rawTestData/Calculation/MathTrig/ODD.data');
    }

    /**
     * @dataProvider providerFACT
     */
    public function test_fact()
    {
        $args = func_get_args();
        $expectedResult = array_pop($args);
        $result = call_user_func_array(['PHPExcel_Calculation_MathTrig', 'FACT'], $args);
        $this->assertEquals($expectedResult, $result, null, 1E-12);
    }

    public function providerFACT()
    {
        return new testDataFileIterator('rawTestData/Calculation/MathTrig/FACT.data');
    }

    /**
     * @dataProvider providerFACTDOUBLE
     */
    public function test_factdouble()
    {
        $args = func_get_args();
        $expectedResult = array_pop($args);
        $result = call_user_func_array(['PHPExcel_Calculation_MathTrig', 'FACTDOUBLE'], $args);
        $this->assertEquals($expectedResult, $result, null, 1E-12);
    }

    public function providerFACTDOUBLE()
    {
        return new testDataFileIterator('rawTestData/Calculation/MathTrig/FACTDOUBLE.data');
    }

    /**
     * @dataProvider providerFLOOR
     */
    public function test_floor()
    {
        $args = func_get_args();
        $expectedResult = array_pop($args);
        $result = call_user_func_array(['PHPExcel_Calculation_MathTrig', 'FLOOR'], $args);
        $this->assertEquals($expectedResult, $result, null, 1E-12);
    }

    public function providerFLOOR()
    {
        return new testDataFileIterator('rawTestData/Calculation/MathTrig/FLOOR.data');
    }

    /**
     * @dataProvider providerGCD
     */
    public function test_gcd()
    {
        $args = func_get_args();
        $expectedResult = array_pop($args);
        $result = call_user_func_array(['PHPExcel_Calculation_MathTrig', 'GCD'], $args);
        $this->assertEquals($expectedResult, $result, null, 1E-12);
    }

    public function providerGCD()
    {
        return new testDataFileIterator('rawTestData/Calculation/MathTrig/GCD.data');
    }

    /**
     * @dataProvider providerLCM
     */
    public function test_lcm()
    {
        $args = func_get_args();
        $expectedResult = array_pop($args);
        $result = call_user_func_array(['PHPExcel_Calculation_MathTrig', 'LCM'], $args);
        $this->assertEquals($expectedResult, $result, null, 1E-12);
    }

    public function providerLCM()
    {
        return new testDataFileIterator('rawTestData/Calculation/MathTrig/LCM.data');
    }

    /**
     * @dataProvider providerINT
     */
    public function test_int()
    {
        $args = func_get_args();
        $expectedResult = array_pop($args);
        $result = call_user_func_array(['PHPExcel_Calculation_MathTrig', 'INT'], $args);
        $this->assertEquals($expectedResult, $result);
    }

    public function providerINT()
    {
        return new testDataFileIterator('rawTestData/Calculation/MathTrig/INT.data');
    }

    /**
     * @dataProvider providerSIGN
     */
    public function test_sign()
    {
        $args = func_get_args();
        $expectedResult = array_pop($args);
        $result = call_user_func_array(['PHPExcel_Calculation_MathTrig', 'SIGN'], $args);
        $this->assertEquals($expectedResult, $result, null, 1E-12);
    }

    public function providerSIGN()
    {
        return new testDataFileIterator('rawTestData/Calculation/MathTrig/SIGN.data');
    }

    /**
     * @dataProvider providerPOWER
     */
    public function test_power()
    {
        $args = func_get_args();
        $expectedResult = array_pop($args);
        $result = call_user_func_array(['PHPExcel_Calculation_MathTrig', 'POWER'], $args);
        $this->assertEquals($expectedResult, $result, null, 1E-12);
    }

    public function providerPOWER()
    {
        return new testDataFileIterator('rawTestData/Calculation/MathTrig/POWER.data');
    }

    /**
     * @dataProvider providerLOG
     */
    public function test_log()
    {
        $args = func_get_args();
        $expectedResult = array_pop($args);
        $result = call_user_func_array(['PHPExcel_Calculation_MathTrig', 'LOG_BASE'], $args);
        $this->assertEquals($expectedResult, $result, null, 1E-12);
    }

    public function providerLOG()
    {
        return new testDataFileIterator('rawTestData/Calculation/MathTrig/LOG.data');
    }

    /**
     * @dataProvider providerMOD
     */
    public function test_mod()
    {
        $args = func_get_args();
        $expectedResult = array_pop($args);
        $result = call_user_func_array(['PHPExcel_Calculation_MathTrig', 'MOD'], $args);
        $this->assertEquals($expectedResult, $result, null, 1E-12);
    }

    public function providerMOD()
    {
        return new testDataFileIterator('rawTestData/Calculation/MathTrig/MOD.data');
    }

    /**
     * @dataProvider providerMDETERM
     */
    public function test_mdeterm()
    {
        $args = func_get_args();
        $expectedResult = array_pop($args);
        $result = call_user_func_array(['PHPExcel_Calculation_MathTrig', 'MDETERM'], $args);
        $this->assertEquals($expectedResult, $result, null, 1E-12);
    }

    public function providerMDETERM()
    {
        return new testDataFileIterator('rawTestData/Calculation/MathTrig/MDETERM.data');
    }

    /**
     * @dataProvider providerMINVERSE
     */
    public function test_minverse()
    {
        $args = func_get_args();
        $expectedResult = array_pop($args);
        $result = call_user_func_array(['PHPExcel_Calculation_MathTrig', 'MINVERSE'], $args);
        $this->assertEquals($expectedResult, $result, null, 1E-12);
    }

    public function providerMINVERSE()
    {
        return new testDataFileIterator('rawTestData/Calculation/MathTrig/MINVERSE.data');
    }

    /**
     * @dataProvider providerMMULT
     */
    public function test_mmult()
    {
        $args = func_get_args();
        $expectedResult = array_pop($args);
        $result = call_user_func_array(['PHPExcel_Calculation_MathTrig', 'MMULT'], $args);
        $this->assertEquals($expectedResult, $result, null, 1E-12);
    }

    public function providerMMULT()
    {
        return new testDataFileIterator('rawTestData/Calculation/MathTrig/MMULT.data');
    }

    /**
     * @dataProvider providerMULTINOMIAL
     */
    public function test_multinomial()
    {
        $args = func_get_args();
        $expectedResult = array_pop($args);
        $result = call_user_func_array(['PHPExcel_Calculation_MathTrig', 'MULTINOMIAL'], $args);
        $this->assertEquals($expectedResult, $result, null, 1E-12);
    }

    public function providerMULTINOMIAL()
    {
        return new testDataFileIterator('rawTestData/Calculation/MathTrig/MULTINOMIAL.data');
    }

    /**
     * @dataProvider providerMROUND
     */
    public function test_mround()
    {
        $args = func_get_args();
        $expectedResult = array_pop($args);
        PHPExcel_Calculation::setArrayReturnType(PHPExcel_Calculation::RETURN_ARRAY_AS_VALUE);
        $result = call_user_func_array(['PHPExcel_Calculation_MathTrig', 'MROUND'], $args);
        PHPExcel_Calculation::setArrayReturnType(PHPExcel_Calculation::RETURN_ARRAY_AS_ARRAY);
        $this->assertEquals($expectedResult, $result, null, 1E-12);
    }

    public function providerMROUND()
    {
        return new testDataFileIterator('rawTestData/Calculation/MathTrig/MROUND.data');
    }

    /**
     * @dataProvider providerPRODUCT
     */
    public function test_product()
    {
        $args = func_get_args();
        $expectedResult = array_pop($args);
        $result = call_user_func_array(['PHPExcel_Calculation_MathTrig', 'PRODUCT'], $args);
        $this->assertEquals($expectedResult, $result, null, 1E-12);
    }

    public function providerPRODUCT()
    {
        return new testDataFileIterator('rawTestData/Calculation/MathTrig/PRODUCT.data');
    }

    /**
     * @dataProvider providerQUOTIENT
     */
    public function test_quotient()
    {
        $args = func_get_args();
        $expectedResult = array_pop($args);
        $result = call_user_func_array(['PHPExcel_Calculation_MathTrig', 'QUOTIENT'], $args);
        $this->assertEquals($expectedResult, $result, null, 1E-12);
    }

    public function providerQUOTIENT()
    {
        return new testDataFileIterator('rawTestData/Calculation/MathTrig/QUOTIENT.data');
    }

    /**
     * @dataProvider providerROUNDUP
     */
    public function test_roundup()
    {
        $args = func_get_args();
        $expectedResult = array_pop($args);
        $result = call_user_func_array(['PHPExcel_Calculation_MathTrig', 'ROUNDUP'], $args);
        $this->assertEquals($expectedResult, $result, null, 1E-12);
    }

    public function providerROUNDUP()
    {
        return new testDataFileIterator('rawTestData/Calculation/MathTrig/ROUNDUP.data');
    }

    /**
     * @dataProvider providerROUNDDOWN
     */
    public function test_rounddown()
    {
        $args = func_get_args();
        $expectedResult = array_pop($args);
        $result = call_user_func_array(['PHPExcel_Calculation_MathTrig', 'ROUNDDOWN'], $args);
        $this->assertEquals($expectedResult, $result, null, 1E-12);
    }

    public function providerROUNDDOWN()
    {
        return new testDataFileIterator('rawTestData/Calculation/MathTrig/ROUNDDOWN.data');
    }

    /**
     * @dataProvider providerSERIESSUM
     */
    public function test_seriessum()
    {
        $args = func_get_args();
        $expectedResult = array_pop($args);
        $result = call_user_func_array(['PHPExcel_Calculation_MathTrig', 'SERIESSUM'], $args);
        $this->assertEquals($expectedResult, $result, null, 1E-12);
    }

    public function providerSERIESSUM()
    {
        return new testDataFileIterator('rawTestData/Calculation/MathTrig/SERIESSUM.data');
    }

    /**
     * @dataProvider providerSUMSQ
     */
    public function test_sumsq()
    {
        $args = func_get_args();
        $expectedResult = array_pop($args);
        $result = call_user_func_array(['PHPExcel_Calculation_MathTrig', 'SUMSQ'], $args);
        $this->assertEquals($expectedResult, $result, null, 1E-12);
    }

    public function providerSUMSQ()
    {
        return new testDataFileIterator('rawTestData/Calculation/MathTrig/SUMSQ.data');
    }

    /**
     * @dataProvider providerTRUNC
     */
    public function test_trunc()
    {
        $args = func_get_args();
        $expectedResult = array_pop($args);
        $result = call_user_func_array(['PHPExcel_Calculation_MathTrig', 'TRUNC'], $args);
        $this->assertEquals($expectedResult, $result, null, 1E-12);
    }

    public function providerTRUNC()
    {
        return new testDataFileIterator('rawTestData/Calculation/MathTrig/TRUNC.data');
    }

    /**
     * @dataProvider providerROMAN
     */
    public function test_roman()
    {
        $args = func_get_args();
        $expectedResult = array_pop($args);
        $result = call_user_func_array(['PHPExcel_Calculation_MathTrig', 'ROMAN'], $args);
        $this->assertEquals($expectedResult, $result);
    }

    public function providerROMAN()
    {
        return new testDataFileIterator('rawTestData/Calculation/MathTrig/ROMAN.data');
    }

    /**
     * @dataProvider providerSQRTPI
     */
    public function test_sqrtpi()
    {
        $args = func_get_args();
        $expectedResult = array_pop($args);
        $result = call_user_func_array(['PHPExcel_Calculation_MathTrig', 'SQRTPI'], $args);
        $this->assertEquals($expectedResult, $result, null, 1E-12);
    }

    public function providerSQRTPI()
    {
        return new testDataFileIterator('rawTestData/Calculation/MathTrig/SQRTPI.data');
    }

    /**
     * @dataProvider providerSUMIF
     */
    public function test_sumif()
    {
        $args = func_get_args();
        $expectedResult = array_pop($args);
        $result = call_user_func_array(['PHPExcel_Calculation_MathTrig', 'SUMIF'], $args);
        $this->assertEquals($expectedResult, $result, null, 1E-12);
    }

    public function providerSUMIF()
    {
        return [
            [
                [
                    [1],
                    [5],
                    [10],
                ],
                '>=5',
                15,
            ],
            [
                [
                    ['text'],
                    [2],
                ],
                '=text',
                [
                    [10],
                    [100],
                ],
                10,
            ],
            [
                [
                    ['"text with quotes"'],
                    [2],
                ],
                '="text with quotes"',
                [
                    [10],
                    [100],
                ],
                10,
            ],
            [
                [
                    ['"text with quotes"'],
                    [''],
                ],
                '>"', // Compare to the single characater " (double quote)
                [
                    [10],
                    [100],
                ],
                10,
            ],
            [
                [
                    [''],
                    ['anything'],
                ],
                '>"', // Compare to the single characater " (double quote)
                [
                    [10],
                    [100],
                ],
                100,
            ],
        ];
    }
}
