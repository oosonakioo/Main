<?php

class RowIteratorTest extends PHPUnit_Framework_TestCase
{
    public $mockWorksheet;

    public $mockRow;

    protected function setUp()
    {
        if (! defined('PHPEXCEL_ROOT')) {
            define('PHPEXCEL_ROOT', APPLICATION_PATH.'/');
        }
        require_once PHPEXCEL_ROOT.'PHPExcel/Autoloader.php';

        $this->mockRow = $this->getMockBuilder('PHPExcel_Worksheet_Row')
            ->disableOriginalConstructor()
            ->getMock();

        $this->mockWorksheet = $this->getMockBuilder('PHPExcel_Worksheet')
            ->disableOriginalConstructor()
            ->getMock();

        $this->mockWorksheet->expects($this->any())
            ->method('getHighestRow')
            ->will($this->returnValue(5));
        $this->mockWorksheet->expects($this->any())
            ->method('current')
            ->will($this->returnValue($this->mockRow));
    }

    public function test_iterator_full_range()
    {
        $iterator = new PHPExcel_Worksheet_RowIterator($this->mockWorksheet);
        $rowIndexResult = 1;
        $this->assertEquals($rowIndexResult, $iterator->key());

        foreach ($iterator as $key => $row) {
            $this->assertEquals($rowIndexResult++, $key);
            $this->assertInstanceOf('PHPExcel_Worksheet_Row', $row);
        }
    }

    public function test_iterator_start_end_range()
    {
        $iterator = new PHPExcel_Worksheet_RowIterator($this->mockWorksheet, 2, 4);
        $rowIndexResult = 2;
        $this->assertEquals($rowIndexResult, $iterator->key());

        foreach ($iterator as $key => $row) {
            $this->assertEquals($rowIndexResult++, $key);
            $this->assertInstanceOf('PHPExcel_Worksheet_Row', $row);
        }
    }

    public function test_iterator_seek_and_prev()
    {
        $iterator = new PHPExcel_Worksheet_RowIterator($this->mockWorksheet, 2, 4);
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
        $iterator = new PHPExcel_Worksheet_RowIterator($this->mockWorksheet, 2, 4);
        $iterator->seek(1);
    }

    /**
     * @expectedException PHPExcel_Exception
     */
    public function test_prev_out_of_range()
    {
        $iterator = new PHPExcel_Worksheet_RowIterator($this->mockWorksheet, 2, 4);
        $iterator->prev();
    }
}
