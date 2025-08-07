<?php

require_once 'testDataFileIterator.php';

class CellTest extends PHPUnit_Framework_TestCase
{
    protected function setUp()
    {
        if (! defined('PHPEXCEL_ROOT')) {
            define('PHPEXCEL_ROOT', APPLICATION_PATH.'/');
        }
        require_once PHPEXCEL_ROOT.'PHPExcel/Autoloader.php';
    }

    /**
     * @dataProvider providerColumnString
     */
    public function test_column_index_from_string()
    {
        $args = func_get_args();
        $expectedResult = array_pop($args);
        $result = call_user_func_array(['PHPExcel_Cell', 'columnIndexFromString'], $args);
        $this->assertEquals($expectedResult, $result);
    }

    public function providerColumnString()
    {
        return new testDataFileIterator('rawTestData/ColumnString.data');
    }

    public function test_column_index_from_string_too_long()
    {
        $cellAddress = 'ABCD';
        try {
            $result = call_user_func(['PHPExcel_Cell', 'columnIndexFromString'], $cellAddress);
        } catch (PHPExcel_Exception $e) {
            $this->assertEquals($e->getMessage(), 'Column string index can not be longer than 3 characters');

            return;
        }
        $this->fail('An expected exception has not been raised.');
    }

    public function test_column_index_from_string_too_short()
    {
        $cellAddress = '';
        try {
            $result = call_user_func(['PHPExcel_Cell', 'columnIndexFromString'], $cellAddress);
        } catch (PHPExcel_Exception $e) {
            $this->assertEquals($e->getMessage(), 'Column string index can not be empty');

            return;
        }
        $this->fail('An expected exception has not been raised.');
    }

    /**
     * @dataProvider providerColumnIndex
     */
    public function test_string_from_column_index()
    {
        $args = func_get_args();
        $expectedResult = array_pop($args);
        $result = call_user_func_array(['PHPExcel_Cell', 'stringFromColumnIndex'], $args);
        $this->assertEquals($expectedResult, $result);
    }

    public function providerColumnIndex()
    {
        return new testDataFileIterator('rawTestData/ColumnIndex.data');
    }

    /**
     * @dataProvider providerCoordinates
     */
    public function test_coordinate_from_string()
    {
        $args = func_get_args();
        $expectedResult = array_pop($args);
        $result = call_user_func_array(['PHPExcel_Cell', 'coordinateFromString'], $args);
        $this->assertEquals($expectedResult, $result);
    }

    public function providerCoordinates()
    {
        return new testDataFileIterator('rawTestData/CellCoordinates.data');
    }

    public function test_coordinate_from_string_with_range_address()
    {
        $cellAddress = 'A1:AI2012';
        try {
            $result = call_user_func(['PHPExcel_Cell', 'coordinateFromString'], $cellAddress);
        } catch (PHPExcel_Exception $e) {
            $this->assertEquals($e->getMessage(), 'Cell coordinate string can not be a range of cells');

            return;
        }
        $this->fail('An expected exception has not been raised.');
    }

    public function test_coordinate_from_string_with_empty_address()
    {
        $cellAddress = '';
        try {
            $result = call_user_func(['PHPExcel_Cell', 'coordinateFromString'], $cellAddress);
        } catch (PHPExcel_Exception $e) {
            $this->assertEquals($e->getMessage(), 'Cell coordinate can not be zero-length string');

            return;
        }
        $this->fail('An expected exception has not been raised.');
    }

    public function test_coordinate_from_string_with_invalid_address()
    {
        $cellAddress = 'AI';
        try {
            $result = call_user_func(['PHPExcel_Cell', 'coordinateFromString'], $cellAddress);
        } catch (PHPExcel_Exception $e) {
            $this->assertEquals($e->getMessage(), 'Invalid cell coordinate '.$cellAddress);

            return;
        }
        $this->fail('An expected exception has not been raised.');
    }

    /**
     * @dataProvider providerAbsoluteCoordinates
     */
    public function test_absolute_coordinate_from_string()
    {
        $args = func_get_args();
        $expectedResult = array_pop($args);
        $result = call_user_func_array(['PHPExcel_Cell', 'absoluteCoordinate'], $args);
        $this->assertEquals($expectedResult, $result);
    }

    public function providerAbsoluteCoordinates()
    {
        return new testDataFileIterator('rawTestData/CellAbsoluteCoordinate.data');
    }

    public function test_absolute_coordinate_from_string_with_range_address()
    {
        $cellAddress = 'A1:AI2012';
        try {
            $result = call_user_func(['PHPExcel_Cell', 'absoluteCoordinate'], $cellAddress);
        } catch (PHPExcel_Exception $e) {
            $this->assertEquals($e->getMessage(), 'Cell coordinate string can not be a range of cells');

            return;
        }
        $this->fail('An expected exception has not been raised.');
    }

    /**
     * @dataProvider providerAbsoluteReferences
     */
    public function test_absolute_reference_from_string()
    {
        $args = func_get_args();
        $expectedResult = array_pop($args);
        $result = call_user_func_array(['PHPExcel_Cell', 'absoluteReference'], $args);
        $this->assertEquals($expectedResult, $result);
    }

    public function providerAbsoluteReferences()
    {
        return new testDataFileIterator('rawTestData/CellAbsoluteReference.data');
    }

    public function test_absolute_reference_from_string_with_range_address()
    {
        $cellAddress = 'A1:AI2012';
        try {
            $result = call_user_func(['PHPExcel_Cell', 'absoluteReference'], $cellAddress);
        } catch (PHPExcel_Exception $e) {
            $this->assertEquals($e->getMessage(), 'Cell coordinate string can not be a range of cells');

            return;
        }
        $this->fail('An expected exception has not been raised.');
    }

    /**
     * @dataProvider providerSplitRange
     */
    public function test_split_range()
    {
        $args = func_get_args();
        $expectedResult = array_pop($args);
        $result = call_user_func_array(['PHPExcel_Cell', 'splitRange'], $args);
        foreach ($result as $key => $split) {
            if (! is_array($expectedResult[$key])) {
                $this->assertEquals($expectedResult[$key], $split[0]);
            } else {
                $this->assertEquals($expectedResult[$key], $split);
            }
        }
    }

    public function providerSplitRange()
    {
        return new testDataFileIterator('rawTestData/CellSplitRange.data');
    }

    /**
     * @dataProvider providerBuildRange
     */
    public function test_build_range()
    {
        $args = func_get_args();
        $expectedResult = array_pop($args);
        $result = call_user_func_array(['PHPExcel_Cell', 'buildRange'], $args);
        $this->assertEquals($expectedResult, $result);
    }

    public function providerBuildRange()
    {
        return new testDataFileIterator('rawTestData/CellBuildRange.data');
    }

    public function test_build_range_invalid()
    {
        $cellRange = '';
        try {
            $result = call_user_func(['PHPExcel_Cell', 'buildRange'], $cellRange);
        } catch (PHPExcel_Exception $e) {
            $this->assertEquals($e->getMessage(), 'Range does not contain any information');

            return;
        }
        $this->fail('An expected exception has not been raised.');
    }

    /**
     * @dataProvider providerRangeBoundaries
     */
    public function test_range_boundaries()
    {
        $args = func_get_args();
        $expectedResult = array_pop($args);
        $result = call_user_func_array(['PHPExcel_Cell', 'rangeBoundaries'], $args);
        $this->assertEquals($expectedResult, $result);
    }

    public function providerRangeBoundaries()
    {
        return new testDataFileIterator('rawTestData/CellRangeBoundaries.data');
    }

    /**
     * @dataProvider providerRangeDimension
     */
    public function test_range_dimension()
    {
        $args = func_get_args();
        $expectedResult = array_pop($args);
        $result = call_user_func_array(['PHPExcel_Cell', 'rangeDimension'], $args);
        $this->assertEquals($expectedResult, $result);
    }

    public function providerRangeDimension()
    {
        return new testDataFileIterator('rawTestData/CellRangeDimension.data');
    }

    /**
     * @dataProvider providerGetRangeBoundaries
     */
    public function test_get_range_boundaries()
    {
        $args = func_get_args();
        $expectedResult = array_pop($args);
        $result = call_user_func_array(['PHPExcel_Cell', 'getRangeBoundaries'], $args);
        $this->assertEquals($expectedResult, $result);
    }

    public function providerGetRangeBoundaries()
    {
        return new testDataFileIterator('rawTestData/CellGetRangeBoundaries.data');
    }

    /**
     * @dataProvider providerExtractAllCellReferencesInRange
     */
    public function test_extract_all_cell_references_in_range()
    {
        $args = func_get_args();
        $expectedResult = array_pop($args);
        $result = call_user_func_array(['PHPExcel_Cell', 'extractAllCellReferencesInRange'], $args);
        $this->assertEquals($expectedResult, $result);
    }

    public function providerExtractAllCellReferencesInRange()
    {
        return new testDataFileIterator('rawTestData/CellExtractAllCellReferencesInRange.data');
    }
}
