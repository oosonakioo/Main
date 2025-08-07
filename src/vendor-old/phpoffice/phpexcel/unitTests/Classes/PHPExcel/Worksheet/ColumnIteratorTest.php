<?php

class ColumnIteratorTest extends PHPUnit_Framework_TestCase
{
    public $mockWorksheet;

    public $mockColumn;

    protected function setUp()
    {
        if (! defined('PHPEXCEL_ROOT')) {
            define('PHPEXCEL_ROOT', APPLICATION_PATH.'/');
        }
        require_once PHPEXCEL_ROOT.'PHPExcel/Autoloader.php';

        $this->mockColumn = $this->getMockBuilder('PHPExcel_Worksheet_Column')
            ->disableOriginalConstructor()
            ->getMock();

        $this->mockWorksheet = $this->getMockBuilder('PHPExcel_Worksheet')
            ->disableOriginalConstructor()
            ->getMock();

        $this->mockWorksheet->expects($this->any())
            ->method('getHighestColumn')
            ->will($this->returnValue('E'));
        $this->mockWorksheet->expects($this->any())
            ->method('current')
            ->will($this->returnValue($this->mockColumn));
    }

    public function test_iterator_full_range()
    {
        $iterator = new PHPExcel_Worksheet_ColumnIterator($this->mockWorksheet);
        $columnIndexResult = 'A';
        $this->assertEquals($columnIndexResult, $iterator->key());

        foreach ($iterator as $key => $column) {
            $this->assertEquals($columnIndexResult++, $key);
            $this->assertInstanceOf('PHPExcel_Worksheet_Column', $column);
        }
    }

    public function test_iterator_start_end_range()
    {
        $iterator = new PHPExcel_Worksheet_ColumnIterator($this->mockWorksheet, 'B', 'D');
        $columnIndexResult = 'B';
        $this->assertEquals($columnIndexResult, $iterator->key());

        foreach ($iterator as $key => $column) {
            $this->assertEquals($columnIndexResult++, $key);
            $this->assertInstanceOf('PHPExcel_Worksheet_Column', $column);
        }
    }

    public function test_iterator_seek_and_prev()
    {
        $ranges = range('A', 'E');
        $iterator = new PHPExcel_Worksheet_ColumnIterator($this->mockWorksheet, 'B', 'D');
        $columnIndexResult = 'D';
        $iterator->seek('D');
        $this->assertEquals($columnIndexResult, $iterator->key());

        for ($i = 1; $i < array_search($columnIndexResult, $ranges); $i++) {
            $iterator->prev();
            $expectedResult = $ranges[array_search($columnIndexResult, $ranges) - $i];
            $this->assertEquals($expectedResult, $iterator->key());
        }
    }

    /**
     * @expectedException PHPExcel_Exception
     */
    public function test_seek_out_of_range()
    {
        $iterator = new PHPExcel_Worksheet_ColumnIterator($this->mockWorksheet, 'B', 'D');
        $iterator->seek('A');
    }

    /**
     * @expectedException PHPExcel_Exception
     */
    public function test_prev_out_of_range()
    {
        $iterator = new PHPExcel_Worksheet_ColumnIterator($this->mockWorksheet, 'B', 'D');
        $iterator->prev();
    }
}
