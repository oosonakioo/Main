<?php

class HyperlinkTest extends PHPUnit_Framework_TestCase
{
    protected function setUp()
    {
        if (! defined('PHPEXCEL_ROOT')) {
            define('PHPEXCEL_ROOT', APPLICATION_PATH.'/');
        }
        require_once PHPEXCEL_ROOT.'PHPExcel/Autoloader.php';
    }

    public function test_get_url()
    {
        $urlValue = 'http://www.phpexcel.net';

        $testInstance = new PHPExcel_Cell_Hyperlink($urlValue);

        $result = $testInstance->getUrl();
        $this->assertEquals($urlValue, $result);
    }

    public function test_set_url()
    {
        $initialUrlValue = 'http://www.phpexcel.net';
        $newUrlValue = 'http://github.com/PHPOffice/PHPExcel';

        $testInstance = new PHPExcel_Cell_Hyperlink($initialUrlValue);
        $result = $testInstance->setUrl($newUrlValue);
        $this->assertTrue($result instanceof PHPExcel_Cell_Hyperlink);

        $result = $testInstance->getUrl();
        $this->assertEquals($newUrlValue, $result);
    }

    public function test_get_tooltip()
    {
        $tooltipValue = 'PHPExcel Web Site';

        $testInstance = new PHPExcel_Cell_Hyperlink(null, $tooltipValue);

        $result = $testInstance->getTooltip();
        $this->assertEquals($tooltipValue, $result);
    }

    public function test_set_tooltip()
    {
        $initialTooltipValue = 'PHPExcel Web Site';
        $newTooltipValue = 'PHPExcel Repository on Github';

        $testInstance = new PHPExcel_Cell_Hyperlink(null, $initialTooltipValue);
        $result = $testInstance->setTooltip($newTooltipValue);
        $this->assertTrue($result instanceof PHPExcel_Cell_Hyperlink);

        $result = $testInstance->getTooltip();
        $this->assertEquals($newTooltipValue, $result);
    }

    public function test_is_internal()
    {
        $initialUrlValue = 'http://www.phpexcel.net';
        $newUrlValue = 'sheet://Worksheet1!A1';

        $testInstance = new PHPExcel_Cell_Hyperlink($initialUrlValue);
        $result = $testInstance->isInternal();
        $this->assertFalse($result);

        $testInstance->setUrl($newUrlValue);
        $result = $testInstance->isInternal();
        $this->assertTrue($result);
    }

    public function test_get_hash_code()
    {
        $urlValue = 'http://www.phpexcel.net';
        $tooltipValue = 'PHPExcel Web Site';
        $initialExpectedHash = 'd84d713aed1dbbc8a7c5af183d6c7dbb';

        $testInstance = new PHPExcel_Cell_Hyperlink($urlValue, $tooltipValue);

        $result = $testInstance->getHashCode();
        $this->assertEquals($initialExpectedHash, $result);
    }
}
