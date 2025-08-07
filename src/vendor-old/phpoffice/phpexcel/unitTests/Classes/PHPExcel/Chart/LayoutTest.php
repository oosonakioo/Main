<?php

class LayoutTest extends PHPUnit_Framework_TestCase
{
    protected function setUp()
    {
        if (! defined('PHPEXCEL_ROOT')) {
            define('PHPEXCEL_ROOT', APPLICATION_PATH.'/');
        }
        require_once PHPEXCEL_ROOT.'PHPExcel/Autoloader.php';
    }

    public function test_set_layout_target()
    {
        $LayoutTargetValue = 'String';

        $testInstance = new PHPExcel_Chart_Layout;

        $result = $testInstance->setLayoutTarget($LayoutTargetValue);
        $this->assertTrue($result instanceof PHPExcel_Chart_Layout);
    }

    public function test_get_layout_target()
    {
        $LayoutTargetValue = 'String';

        $testInstance = new PHPExcel_Chart_Layout;
        $setValue = $testInstance->setLayoutTarget($LayoutTargetValue);

        $result = $testInstance->getLayoutTarget();
        $this->assertEquals($LayoutTargetValue, $result);
    }
}
