<?php

class AutofilterColumnTest extends PHPUnit_Framework_TestCase
{
    private $_testInitialColumn = 'H';

    private $_testAutoFilterColumnObject;

    private $_mockAutoFilterObject;

    protected function setUp()
    {
        if (! defined('PHPEXCEL_ROOT')) {
            define('PHPEXCEL_ROOT', APPLICATION_PATH.'/');
        }
        require_once PHPEXCEL_ROOT.'PHPExcel/Autoloader.php';

        $this->_mockAutoFilterObject = $this->getMockBuilder('PHPExcel_Worksheet_AutoFilter')
            ->disableOriginalConstructor()
            ->getMock();

        $this->_mockAutoFilterObject->expects($this->any())
            ->method('testColumnInRange')
            ->will($this->returnValue(3));

        $this->_testAutoFilterColumnObject = new PHPExcel_Worksheet_AutoFilter_Column(
            $this->_testInitialColumn,
            $this->_mockAutoFilterObject
        );
    }

    public function test_get_column_index()
    {
        $result = $this->_testAutoFilterColumnObject->getColumnIndex();
        $this->assertEquals($this->_testInitialColumn, $result);
    }

    public function test_set_column_index()
    {
        $expectedResult = 'L';

        //	Setters return the instance to implement the fluent interface
        $result = $this->_testAutoFilterColumnObject->setColumnIndex($expectedResult);
        $this->assertInstanceOf('PHPExcel_Worksheet_AutoFilter_Column', $result);

        $result = $this->_testAutoFilterColumnObject->getColumnIndex();
        $this->assertEquals($expectedResult, $result);
    }

    public function test_get_parent()
    {
        $result = $this->_testAutoFilterColumnObject->getParent();
        $this->assertInstanceOf('PHPExcel_Worksheet_AutoFilter', $result);
    }

    public function test_set_parent()
    {
        //	Setters return the instance to implement the fluent interface
        $result = $this->_testAutoFilterColumnObject->setParent($this->_mockAutoFilterObject);
        $this->assertInstanceOf('PHPExcel_Worksheet_AutoFilter_Column', $result);
    }

    public function test_get_filter_type()
    {
        $result = $this->_testAutoFilterColumnObject->getFilterType();
        $this->assertEquals(PHPExcel_Worksheet_AutoFilter_Column::AUTOFILTER_FILTERTYPE_FILTER, $result);
    }

    public function test_set_filter_type()
    {
        $result = $this->_testAutoFilterColumnObject->setFilterType(PHPExcel_Worksheet_AutoFilter_Column::AUTOFILTER_FILTERTYPE_DYNAMICFILTER);
        $this->assertInstanceOf('PHPExcel_Worksheet_AutoFilter_Column', $result);

        $result = $this->_testAutoFilterColumnObject->getFilterType();
        $this->assertEquals(PHPExcel_Worksheet_AutoFilter_Column::AUTOFILTER_FILTERTYPE_DYNAMICFILTER, $result);
    }

    /**
     * @expectedException PHPExcel_Exception
     */
    public function test_set_invalid_filter_type_throws_exception()
    {
        $expectedResult = 'Unfiltered';

        $result = $this->_testAutoFilterColumnObject->setFilterType($expectedResult);
    }

    public function test_get_join()
    {
        $result = $this->_testAutoFilterColumnObject->getJoin();
        $this->assertEquals(PHPExcel_Worksheet_AutoFilter_Column::AUTOFILTER_COLUMN_JOIN_OR, $result);
    }

    public function test_set_join()
    {
        $result = $this->_testAutoFilterColumnObject->setJoin(PHPExcel_Worksheet_AutoFilter_Column::AUTOFILTER_COLUMN_JOIN_AND);
        $this->assertInstanceOf('PHPExcel_Worksheet_AutoFilter_Column', $result);

        $result = $this->_testAutoFilterColumnObject->getJoin();
        $this->assertEquals(PHPExcel_Worksheet_AutoFilter_Column::AUTOFILTER_COLUMN_JOIN_AND, $result);
    }

    /**
     * @expectedException PHPExcel_Exception
     */
    public function test_set_invalid_join_throws_exception()
    {
        $expectedResult = 'Neither';

        $result = $this->_testAutoFilterColumnObject->setJoin($expectedResult);
    }

    public function test_set_attributes()
    {
        $attributeSet = ['val' => 100,
            'maxVal' => 200,
        ];

        //	Setters return the instance to implement the fluent interface
        $result = $this->_testAutoFilterColumnObject->setAttributes($attributeSet);
        $this->assertInstanceOf('PHPExcel_Worksheet_AutoFilter_Column', $result);
    }

    public function test_get_attributes()
    {
        $attributeSet = ['val' => 100,
            'maxVal' => 200,
        ];

        $this->_testAutoFilterColumnObject->setAttributes($attributeSet);

        $result = $this->_testAutoFilterColumnObject->getAttributes();
        $this->assertTrue(is_array($result));
        $this->assertEquals(count($attributeSet), count($result));
    }

    public function test_set_attribute()
    {
        $attributeSet = ['val' => 100,
            'maxVal' => 200,
        ];

        foreach ($attributeSet as $attributeName => $attributeValue) {
            //	Setters return the instance to implement the fluent interface
            $result = $this->_testAutoFilterColumnObject->setAttribute($attributeName, $attributeValue);
            $this->assertInstanceOf('PHPExcel_Worksheet_AutoFilter_Column', $result);
        }
    }

    public function test_get_attribute()
    {
        $attributeSet = ['val' => 100,
            'maxVal' => 200,
        ];

        $this->_testAutoFilterColumnObject->setAttributes($attributeSet);

        foreach ($attributeSet as $attributeName => $attributeValue) {
            $result = $this->_testAutoFilterColumnObject->getAttribute($attributeName);
            $this->assertEquals($attributeValue, $result);
        }
        $result = $this->_testAutoFilterColumnObject->getAttribute('nonExistentAttribute');
        $this->assertNull($result);
    }

    public function test_clone()
    {
        $result = clone $this->_testAutoFilterColumnObject;
        $this->assertInstanceOf('PHPExcel_Worksheet_AutoFilter_Column', $result);
    }
}
