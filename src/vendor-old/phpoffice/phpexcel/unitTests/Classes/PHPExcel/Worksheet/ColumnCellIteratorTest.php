<?php

class ColumnCellIteratorTest extends PHPUnit_Framework_TestCase
{
    public $mockWorksheet;

    public $mockColumnCell;

    protected function setUp()
    {
        if (! defined('PHPEXCEL_ROOT')) {
            define('PHPEXCEL_ROOT', APPLICATION_PATH.'/');
        }
        require_once PHPEXCEL_ROOT.'PHPExcel/Autoloader.php';

        $this->mockCell = $this->getMockBuilder('PHPExcel_Cell')
            ->disableOriginalConstructor()
            ->getMock();

        $this->mockWorksheet = $this->getMockBuilder('PHPExcel_Worksheet')
            ->disableOriginalConstructor()
            ->getMock();

        $this->mockWorksheet->expects($this->any())
            ->method('getHighestRow')
            ->will($this->returnValue(5));
        $this->mockWorksheet->expects($this->any())
            ->method('getCellByColumnAndRow')
            ->will($this->returnValue($this->mockCell));
    }

    public function test_iterator_full_range()
    {
        $iterator = new PHPExcel_Worksheet_ColumnCellIterator($this->mockWorksheet, 'A');
        $ColumnCellIndexResult = 1;
        $this->assertEquals($ColumnCellIndexResult, $iterator->key());

        foreach ($iterator as $key => $ColumnCell) {
            $this->assertEquals($ColumnCellIndexResult++, $key);
            $this->assertInstanceOf('PHPExcel_Cell', $ColumnCell);
        }
    }

    public function test_iterator_start_end_range()
    {
        $iterator = new PHPExcel_Worksheet_ColumnCellIterator($this->mockWorksheet, 'A', 2, 4);
        $ColumnCellIndexResult = 2;
        $this->assertEquals($ColumnCellIndexResult, $iterator->key());

        foreach ($iterator as $key => $ColumnCell) {
            $this->assertEquals($ColumnCellIndexResult++, $key);
            $this->assertInstanceOf('PHPExcel_Cell', $ColumnCell);
        }
    }

    public function test_iterator_seek_and_prev()
    {
        $iterator = new PHPExcel_Worksheet_ColumnCellIterator($this->mockWorksheet, 'A', 2, 4);
        $columnIndexResult = 4;
        $iterator->seek(4);
        $this->assertEquals($columnIndexResult, $iterator->key());

        for ($i = 1; $i < $columnIndexResult - 1; $i++) {
            $iterator->prev();
            $this->assertEquals($columnIndexResult - $i, $iterator->key());
        }
    }

    /**
     * @expectedException PHPExcel_Exception
     */
    public function test_seek_out_of_range()
    {
        $iterator = new PHPExcel_Worksheet_ColumnCellIterator($this->mockWorksheet, 'A', 2, 4);
        $iterator->seek(1);
    }

    /**
     * @expectedException PHPExcel_Exception
     */
    public function test_prev_out_of_range()
    {
        $iterator = new PHPExcel_Worksheet_ColumnCellIterator($this->mockWorksheet, 'A', 2, 4);
        $iterator->prev();
    }
}
