<?php

require_once 'testDataFileIterator.php';

class FileTest extends PHPUnit_Framework_TestCase
{
    protected function setUp()
    {
        if (! defined('PHPEXCEL_ROOT')) {
            define('PHPEXCEL_ROOT', APPLICATION_PATH.'/');
        }
        require_once PHPEXCEL_ROOT.'PHPExcel/Autoloader.php';
    }

    public function test_get_use_upload_temp_directory()
    {
        $expectedResult = false;

        $result = call_user_func(['PHPExcel_Shared_File', 'getUseUploadTempDirectory']);
        $this->assertEquals($expectedResult, $result);
    }

    public function test_set_use_upload_temp_directory()
    {
        $useUploadTempDirectoryValues = [
            true,
            false,
        ];

        foreach ($useUploadTempDirectoryValues as $useUploadTempDirectoryValue) {
            call_user_func(['PHPExcel_Shared_File', 'setUseUploadTempDirectory'], $useUploadTempDirectoryValue);

            $result = call_user_func(['PHPExcel_Shared_File', 'getUseUploadTempDirectory']);
            $this->assertEquals($useUploadTempDirectoryValue, $result);
        }
    }
}
