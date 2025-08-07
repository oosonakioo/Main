<?php

class WorksheetColumnTest extends PHPUnit_Framework_TestCase
{
    public $mockWorksheet;

    public $mockColumn;

    protected function setUp()
    {
        if (! defined('PHPEXCEL_ROOT')) {
            define('PHPEXCEL_ROOT', APPLICATION_PATH.'/');
        }
        require_once PHPEXCEL_ROOT.'PHPExcel/Autoloader.php';

        $this->mockWorksheet = $this->getMockBuilder('PHPExcel_Worksheet')
            ->disableOriginalConstructor()
            ->getMock();
        $this->mockWorksheet->expects($this->any())
            ->method('getHighestRow')
            ->will($this->returnValue(5));
    }

    public function test_instantiate_column_default()
    {
        $column = new PHPExcel_Worksheet_Column($this->mockWorksheet);
        $this->assertInstanceOf('PHPExcel_Worksheet_Column', $column);
        $columnIndex = $column->getColumnIndex();
        $this->assertEquals('A', $columnIndex);
    }

    public function test_instantiate_column_specified()
    {
        $column = new PHPExcel_Worksheet_Column($this->mockWorksheet, 'E');
        $this->assertInstanceOf('PHPExcel_Worksheet_Column', $column);
        $columnIndex = $column->getColumnIndex();
        $this->assertEquals('E', $columnIndex);
    }

    public function test_get_cell_iterator()
    {
        $column = new PHPExcel_Worksheet_Column($this->mockWorksheet);
        $cellIterator = $column->getCellIterator();
        $this->assertInstanceOf('PHPExcel_Worksheet_ColumnCellIterator', $cellIterator);
    }
}
