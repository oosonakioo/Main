<?php

require_once 'testDataFileIterator.php';

class FinancialTest extends PHPUnit_Framework_TestCase
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
     * @dataProvider providerACCRINT
     */
    public function test_accrint()
    {
        $args = func_get_args();
        $expectedResult = array_pop($args);
        $result = call_user_func_array(['PHPExcel_Calculation_Financial', 'ACCRINT'], $args);
        $this->assertEquals($expectedResult, $result, null, 1E-8);
    }

    public function providerACCRINT()
    {
        return new testDataFileIterator('rawTestData/Calculation/Financial/ACCRINT.data');
    }

    /**
     * @dataProvider providerACCRINTM
     */
    public function test_accrintm()
    {
        $args = func_get_args();
        $expectedResult = array_pop($args);
        $result = call_user_func_array(['PHPExcel_Calculation_Financial', 'ACCRINTM'], $args);
        $this->assertEquals($expectedResult, $result, null, 1E-8);
    }

    public function providerACCRINTM()
    {
        return new testDataFileIterator('rawTestData/Calculation/Financial/ACCRINTM.data');
    }

    /**
     * @dataProvider providerAMORDEGRC
     */
    public function test_amordegrc()
    {
        $args = func_get_args();
        $expectedResult = array_pop($args);
        $result = call_user_func_array(['PHPExcel_Calculation_Financial', 'AMORDEGRC'], $args);
        $this->assertEquals($expectedResult, $result, null, 1E-8);
    }

    public function providerAMORDEGRC()
    {
        return new testDataFileIterator('rawTestData/Calculation/Financial/AMORDEGRC.data');
    }

    /**
     * @dataProvider providerAMORLINC
     */
    public function test_amorlinc()
    {
        $args = func_get_args();
        $expectedResult = array_pop($args);
        $result = call_user_func_array(['PHPExcel_Calculation_Financial', 'AMORLINC'], $args);
        $this->assertEquals($expectedResult, $result, null, 1E-8);
    }

    public function providerAMORLINC()
    {
        return new testDataFileIterator('rawTestData/Calculation/Financial/AMORLINC.data');
    }

    /**
     * @dataProvider providerCOUPDAYBS
     */
    public function test_coupdaybs()
    {
        $args = func_get_args();
        $expectedResult = array_pop($args);
        $result = call_user_func_array(['PHPExcel_Calculation_Financial', 'COUPDAYBS'], $args);
        $this->assertEquals($expectedResult, $result, null, 1E-8);
    }

    public function providerCOUPDAYBS()
    {
        return new testDataFileIterator('rawTestData/Calculation/Financial/COUPDAYBS.data');
    }

    /**
     * @dataProvider providerCOUPDAYS
     */
    public function test_coupdays()
    {
        $args = func_get_args();
        $expectedResult = array_pop($args);
        $result = call_user_func_array(['PHPExcel_Calculation_Financial', 'COUPDAYS'], $args);
        $this->assertEquals($expectedResult, $result, null, 1E-8);
    }

    public function providerCOUPDAYS()
    {
        return new testDataFileIterator('rawTestData/Calculation/Financial/COUPDAYS.data');
    }

    /**
     * @dataProvider providerCOUPDAYSNC
     */
    public function test_coupdaysnc()
    {
        $args = func_get_args();
        $expectedResult = array_pop($args);
        $result = call_user_func_array(['PHPExcel_Calculation_Financial', 'COUPDAYSNC'], $args);
        $this->assertEquals($expectedResult, $result, null, 1E-8);
    }

    public function providerCOUPDAYSNC()
    {
        return new testDataFileIterator('rawTestData/Calculation/Financial/COUPDAYSNC.data');
    }

    /**
     * @dataProvider providerCOUPNCD
     */
    public function test_coupncd()
    {
        $args = func_get_args();
        $expectedResult = array_pop($args);
        $result = call_user_func_array(['PHPExcel_Calculation_Financial', 'COUPNCD'], $args);
        $this->assertEquals($expectedResult, $result, null, 1E-8);
    }

    public function providerCOUPNCD()
    {
        return new testDataFileIterator('rawTestData/Calculation/Financial/COUPNCD.data');
    }

    /**
     * @dataProvider providerCOUPNUM
     */
    public function test_coupnum()
    {
        $args = func_get_args();
        $expectedResult = array_pop($args);
        $result = call_user_func_array(['PHPExcel_Calculation_Financial', 'COUPNUM'], $args);
        $this->assertEquals($expectedResult, $result, null, 1E-8);
    }

    public function providerCOUPNUM()
    {
        return new testDataFileIterator('rawTestData/Calculation/Financial/COUPNUM.data');
    }

    /**
     * @dataProvider providerCOUPPCD
     */
    public function test_couppcd()
    {
        $args = func_get_args();
        $expectedResult = array_pop($args);
        $result = call_user_func_array(['PHPExcel_Calculation_Financial', 'COUPPCD'], $args);
        $this->assertEquals($expectedResult, $result, null, 1E-8);
    }

    public function providerCOUPPCD()
    {
        return new testDataFileIterator('rawTestData/Calculation/Financial/COUPPCD.data');
    }

    /**
     * @dataProvider providerCUMIPMT
     */
    public function test_cumipmt()
    {
        $args = func_get_args();
        $expectedResult = array_pop($args);
        $result = call_user_func_array(['PHPExcel_Calculation_Financial', 'CUMIPMT'], $args);
        $this->assertEquals($expectedResult, $result, null, 1E-8);
    }

    public function providerCUMIPMT()
    {
        return new testDataFileIterator('rawTestData/Calculation/Financial/CUMIPMT.data');
    }

    /**
     * @dataProvider providerCUMPRINC
     */
    public function test_cumprinc()
    {
        $args = func_get_args();
        $expectedResult = array_pop($args);
        $result = call_user_func_array(['PHPExcel_Calculation_Financial', 'CUMPRINC'], $args);
        $this->assertEquals($expectedResult, $result, null, 1E-8);
    }

    public function providerCUMPRINC()
    {
        return new testDataFileIterator('rawTestData/Calculation/Financial/CUMPRINC.data');
    }

    /**
     * @dataProvider providerDB
     */
    public function test_db()
    {
        $args = func_get_args();
        $expectedResult = array_pop($args);
        $result = call_user_func_array(['PHPExcel_Calculation_Financial', 'DB'], $args);
        $this->assertEquals($expectedResult, $result, null, 1E-8);
    }

    public function providerDB()
    {
        return new testDataFileIterator('rawTestData/Calculation/Financial/DB.data');
    }

    /**
     * @dataProvider providerDDB
     */
    public function test_ddb()
    {
        $args = func_get_args();
        $expectedResult = array_pop($args);
        $result = call_user_func_array(['PHPExcel_Calculation_Financial', 'DDB'], $args);
        $this->assertEquals($expectedResult, $result, null, 1E-8);
    }

    public function providerDDB()
    {
        return new testDataFileIterator('rawTestData/Calculation/Financial/DDB.data');
    }

    /**
     * @dataProvider providerDISC
     */
    public function test_disc()
    {
        $args = func_get_args();
        $expectedResult = array_pop($args);
        $result = call_user_func_array(['PHPExcel_Calculation_Financial', 'DISC'], $args);
        $this->assertEquals($expectedResult, $result, null, 1E-8);
    }

    public function providerDISC()
    {
        return new testDataFileIterator('rawTestData/Calculation/Financial/DISC.data');
    }

    /**
     * @dataProvider providerDOLLARDE
     */
    public function test_dollarde()
    {
        $args = func_get_args();
        $expectedResult = array_pop($args);
        $result = call_user_func_array(['PHPExcel_Calculation_Financial', 'DOLLARDE'], $args);
        $this->assertEquals($expectedResult, $result, null, 1E-8);
    }

    public function providerDOLLARDE()
    {
        return new testDataFileIterator('rawTestData/Calculation/Financial/DOLLARDE.data');
    }

    /**
     * @dataProvider providerDOLLARFR
     */
    public function test_dollarfr()
    {
        $args = func_get_args();
        $expectedResult = array_pop($args);
        $result = call_user_func_array(['PHPExcel_Calculation_Financial', 'DOLLARFR'], $args);
        $this->assertEquals($expectedResult, $result, null, 1E-8);
    }

    public function providerDOLLARFR()
    {
        return new testDataFileIterator('rawTestData/Calculation/Financial/DOLLARFR.data');
    }

    /**
     * @dataProvider providerEFFECT
     */
    public function test_effect()
    {
        $args = func_get_args();
        $expectedResult = array_pop($args);
        $result = call_user_func_array(['PHPExcel_Calculation_Financial', 'EFFECT'], $args);
        $this->assertEquals($expectedResult, $result, null, 1E-8);
    }

    public function providerEFFECT()
    {
        return new testDataFileIterator('rawTestData/Calculation/Financial/EFFECT.data');
    }

    /**
     * @dataProvider providerFV
     */
    public function test_fv()
    {
        $args = func_get_args();
        $expectedResult = array_pop($args);
        $result = call_user_func_array(['PHPExcel_Calculation_Financial', 'FV'], $args);
        $this->assertEquals($expectedResult, $result, null, 1E-8);
    }

    public function providerFV()
    {
        return new testDataFileIterator('rawTestData/Calculation/Financial/FV.data');
    }

    /**
     * @dataProvider providerFVSCHEDULE
     */
    public function test_fvschedule()
    {
        $args = func_get_args();
        $expectedResult = array_pop($args);
        $result = call_user_func_array(['PHPExcel_Calculation_Financial', 'FVSCHEDULE'], $args);
        $this->assertEquals($expectedResult, $result, null, 1E-8);
    }

    public function providerFVSCHEDULE()
    {
        return new testDataFileIterator('rawTestData/Calculation/Financial/FVSCHEDULE.data');
    }

    /**
     * @dataProvider providerINTRATE
     */
    public function test_intrate()
    {
        $args = func_get_args();
        $expectedResult = array_pop($args);
        $result = call_user_func_array(['PHPExcel_Calculation_Financial', 'INTRATE'], $args);
        $this->assertEquals($expectedResult, $result, null, 1E-8);
    }

    public function providerINTRATE()
    {
        return new testDataFileIterator('rawTestData/Calculation/Financial/INTRATE.data');
    }

    /**
     * @dataProvider providerIPMT
     */
    public function test_ipmt()
    {
        $args = func_get_args();
        $expectedResult = array_pop($args);
        $result = call_user_func_array(['PHPExcel_Calculation_Financial', 'IPMT'], $args);
        $this->assertEquals($expectedResult, $result, null, 1E-8);
    }

    public function providerIPMT()
    {
        return new testDataFileIterator('rawTestData/Calculation/Financial/IPMT.data');
    }

    /**
     * @dataProvider providerIRR
     */
    public function test_irr()
    {
        $args = func_get_args();
        $expectedResult = array_pop($args);
        $result = call_user_func_array(['PHPExcel_Calculation_Financial', 'IRR'], $args);
        $this->assertEquals($expectedResult, $result, null, 1E-8);
    }

    public function providerIRR()
    {
        return new testDataFileIterator('rawTestData/Calculation/Financial/IRR.data');
    }

    /**
     * @dataProvider providerISPMT
     */
    public function test_ispmt()
    {
        $args = func_get_args();
        $expectedResult = array_pop($args);
        $result = call_user_func_array(['PHPExcel_Calculation_Financial', 'ISPMT'], $args);
        $this->assertEquals($expectedResult, $result, null, 1E-8);
    }

    public function providerISPMT()
    {
        return new testDataFileIterator('rawTestData/Calculation/Financial/ISPMT.data');
    }

    /**
     * @dataProvider providerMIRR
     */
    public function test_mirr()
    {
        $args = func_get_args();
        $expectedResult = array_pop($args);
        $result = call_user_func_array(['PHPExcel_Calculation_Financial', 'MIRR'], $args);
        $this->assertEquals($expectedResult, $result, null, 1E-8);
    }

    public function providerMIRR()
    {
        return new testDataFileIterator('rawTestData/Calculation/Financial/MIRR.data');
    }

    /**
     * @dataProvider providerNOMINAL
     */
    public function test_nominal()
    {
        $args = func_get_args();
        $expectedResult = array_pop($args);
        $result = call_user_func_array(['PHPExcel_Calculation_Financial', 'NOMINAL'], $args);
        $this->assertEquals($expectedResult, $result, null, 1E-8);
    }

    public function providerNOMINAL()
    {
        return new testDataFileIterator('rawTestData/Calculation/Financial/NOMINAL.data');
    }

    /**
     * @dataProvider providerNPER
     */
    public function test_nper()
    {
        $args = func_get_args();
        $expectedResult = array_pop($args);
        $result = call_user_func_array(['PHPExcel_Calculation_Financial', 'NPER'], $args);
        $this->assertEquals($expectedResult, $result, null, 1E-8);
    }

    public function providerNPER()
    {
        return new testDataFileIterator('rawTestData/Calculation/Financial/NPER.data');
    }

    /**
     * @dataProvider providerNPV
     */
    public function test_npv()
    {
        $args = func_get_args();
        $expectedResult = array_pop($args);
        $result = call_user_func_array(['PHPExcel_Calculation_Financial', 'NPV'], $args);
        $this->assertEquals($expectedResult, $result, null, 1E-8);
    }

    public function providerNPV()
    {
        return new testDataFileIterator('rawTestData/Calculation/Financial/NPV.data');
    }

    /**
     * @dataProvider providerPRICE
     */
    public function test_price()
    {
        $args = func_get_args();
        $expectedResult = array_pop($args);
        $result = call_user_func_array(['PHPExcel_Calculation_Financial', 'PRICE'], $args);
        $this->assertEquals($expectedResult, $result, null, 1E-8);
    }

    public function providerPRICE()
    {
        return new testDataFileIterator('rawTestData/Calculation/Financial/PRICE.data');
    }

    /**
     * @dataProvider providerRATE
     */
    public function test_rate()
    {
        $args = func_get_args();
        $expectedResult = array_pop($args);
        $result = call_user_func_array(['PHPExcel_Calculation_Financial', 'RATE'], $args);
        $this->assertEquals($expectedResult, $result, null, 1E-8);
    }

    public function providerRATE()
    {
        return new testDataFileIterator('rawTestData/Calculation/Financial/RATE.data');
    }

    /**
     * @dataProvider providerXIRR
     */
    public function test_xirr()
    {
        $args = func_get_args();
        $expectedResult = array_pop($args);
        $result = call_user_func_array(['PHPExcel_Calculation_Financial', 'XIRR'], $args);
        $this->assertEquals($expectedResult, $result, null, 1E-8);
    }

    public function providerXIRR()
    {
        return new testDataFileIterator('rawTestData/Calculation/Financial/XIRR.data');
    }
}
