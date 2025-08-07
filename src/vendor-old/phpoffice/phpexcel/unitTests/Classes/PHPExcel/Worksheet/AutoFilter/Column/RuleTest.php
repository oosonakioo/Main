<?php

class RuleTest extends PHPUnit_Framework_TestCase
{
    private $_testAutoFilterRuleObject;

    private $_mockAutoFilterColumnObject;

    protected function setUp()
    {
        if (! defined('PHPEXCEL_ROOT')) {
            define('PHPEXCEL_ROOT', APPLICATION_PATH.'/');
        }
        require_once PHPEXCEL_ROOT.'PHPExcel/Autoloader.php';

        $this->_mockAutoFilterColumnObject = $this->getMockBuilder('PHPExcel_Worksheet_AutoFilter_Column')
            ->disableOriginalConstructor()
            ->getMock();

        $this->_mockAutoFilterColumnObject->expects($this->any())
            ->method('testColumnInRange')
            ->will($this->returnValue(3));

        $this->_testAutoFilterRuleObject = new PHPExcel_Worksheet_AutoFilter_Column_Rule(
            $this->_mockAutoFilterColumnObject
        );
    }

    public function test_get_rule_type()
    {
        $result = $this->_testAutoFilterRuleObject->getRuleType();
        $this->assertEquals(PHPExcel_Worksheet_AutoFilter_Column_Rule::AUTOFILTER_RULETYPE_FILTER, $result);
    }

    public function test_set_rule_type()
    {
        $expectedResult = PHPExcel_Worksheet_AutoFilter_Column_Rule::AUTOFILTER_RULETYPE_DATEGROUP;

        //	Setters return the instance to implement the fluent interface
        $result = $this->_testAutoFilterRuleObject->setRuleType($expectedResult);
        $this->assertInstanceOf('PHPExcel_Worksheet_AutoFilter_Column_Rule', $result);

        $result = $this->_testAutoFilterRuleObject->getRuleType();
        $this->assertEquals($expectedResult, $result);
    }

    public function test_set_value()
    {
        $expectedResult = 100;

        //	Setters return the instance to implement the fluent interface
        $result = $this->_testAutoFilterRuleObject->setValue($expectedResult);
        $this->assertInstanceOf('PHPExcel_Worksheet_AutoFilter_Column_Rule', $result);

        $result = $this->_testAutoFilterRuleObject->getValue();
        $this->assertEquals($expectedResult, $result);
    }

    public function test_get_operator()
    {
        $result = $this->_testAutoFilterRuleObject->getOperator();
        $this->assertEquals(PHPExcel_Worksheet_AutoFilter_Column_Rule::AUTOFILTER_COLUMN_RULE_EQUAL, $result);
    }

    public function test_set_operator()
    {
        $expectedResult = PHPExcel_Worksheet_AutoFilter_Column_Rule::AUTOFILTER_COLUMN_RULE_LESSTHAN;

        //	Setters return the instance to implement the fluent interface
        $result = $this->_testAutoFilterRuleObject->setOperator($expectedResult);
        $this->assertInstanceOf('PHPExcel_Worksheet_AutoFilter_Column_Rule', $result);

        $result = $this->_testAutoFilterRuleObject->getOperator();
        $this->assertEquals($expectedResult, $result);
    }

    public function test_set_grouping()
    {
        $expectedResult = PHPExcel_Worksheet_AutoFilter_Column_Rule::AUTOFILTER_RULETYPE_DATEGROUP_MONTH;

        //	Setters return the instance to implement the fluent interface
        $result = $this->_testAutoFilterRuleObject->setGrouping($expectedResult);
        $this->assertInstanceOf('PHPExcel_Worksheet_AutoFilter_Column_Rule', $result);

        $result = $this->_testAutoFilterRuleObject->getGrouping();
        $this->assertEquals($expectedResult, $result);
    }

    public function test_get_parent()
    {
        $result = $this->_testAutoFilterRuleObject->getParent();
        $this->assertInstanceOf('PHPExcel_Worksheet_AutoFilter_Column', $result);
    }

    public function test_set_parent()
    {
        //	Setters return the instance to implement the fluent interface
        $result = $this->_testAutoFilterRuleObject->setParent($this->_mockAutoFilterColumnObject);
        $this->assertInstanceOf('PHPExcel_Worksheet_AutoFilter_Column_Rule', $result);
    }

    public function test_clone()
    {
        $result = clone $this->_testAutoFilterRuleObject;
        $this->assertInstanceOf('PHPExcel_Worksheet_AutoFilter_Column_Rule', $result);
    }
}
