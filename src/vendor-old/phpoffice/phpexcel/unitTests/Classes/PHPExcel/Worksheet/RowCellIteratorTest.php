<?php

class RowCellIteratorTest extends PHPUnit_Framework_TestCase
{
    public $mockWorksheet;

    public $mockRowCell;

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
            ->method('getHighestColumn')
            ->will($this->returnValue('E'));
        $this->mockWorksheet->expects($this->any())
            ->method('getCellByColumnAndRow')
            ->will($this->returnValue($this->mockCell));
    }

    public function test_iterator_full_range()
    {
        $iterator = new PHPExcel_Worksheet_RowCellIterator($this->mockWorksheet);
        $RowCellIndexResult = 'A';
        $this->assertEquals($RowCellIndexResult, $iterator->key());

        foreach ($iterator as $key => $RowCell) {
            $this->assertEquals($RowCellIndexResult++, $key);
            $this->assertInstanceOf('PHPExcel_Cell', $RowCell);
        }
    }

    public function test_iterator_start_end_range()
    {
        $iterator = new PHPExcel_Worksheet_RowCellIterator($this->mockWorksheet, 2, 'B', 'D');
        $RowCellIndexResult = 'B';
        $this->assertEquals($RowCellIndexResult, $iterator->key());

        foreach ($iterator as $key => $RowCell) {
            $this->assertEquals($RowCellIndexResult++, $key);
            $this->assertInstanceOf('PHPExcel_Cell', $RowCell);
        }
    }

    public function test_iterator_seek_and_prev()
    {
        $ranges = range('A', 'E');
        $iterator = new PHPExcel_Worksheet_RowCellIterator($this->mockWorksheet, 2, 'B', 'D');
        $RowCellIndexResult = 'D';
        $iterator->seek('D');
        $this->assertEquals($RowCellIndexResult, $iterator->key());

        for ($i = 1; $i < array_search($RowCellIndexResult, $ranges); $i++) {
            $iterator->prev();
            $expectedResult = $ranges[array_search($RowCellIndexResult, $ranges) - $i];
            $this->assertEquals($expectedResult, $iterator->key());
        }
    }

    /**
     * @expectedException PHPExcel_Exception
     */
    public function test_seek_out_of_range()
    {
        $iterator = new PHPExcel_Worksheet_RowCellIterator($this->mockWorksheet, 2, 'B', 'D');
        $iterator->seek(1);
    }

    /**
     * @expectedException PHPExcel_Exception
     */
    public function test_prev_out_of_range()
    {
        $iterator = new PHPExcel_Worksheet_RowCellIterator($this->mockWorksheet, 2, 'B', 'D');
        $iterator->prev();
    }
}
