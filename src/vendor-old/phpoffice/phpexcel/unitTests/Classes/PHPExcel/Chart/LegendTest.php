<?php

class LegendTest extends PHPUnit_Framework_TestCase
{
    protected function setUp()
    {
        if (! defined('PHPEXCEL_ROOT')) {
            define('PHPEXCEL_ROOT', APPLICATION_PATH.'/');
        }
        require_once PHPEXCEL_ROOT.'PHPExcel/Autoloader.php';
    }

    public function test_set_position()
    {
        $positionValues = [
            PHPExcel_Chart_Legend::POSITION_RIGHT,
            PHPExcel_Chart_Legend::POSITION_LEFT,
            PHPExcel_Chart_Legend::POSITION_TOP,
            PHPExcel_Chart_Legend::POSITION_BOTTOM,
            PHPExcel_Chart_Legend::POSITION_TOPRIGHT,
        ];

        $testInstance = new PHPExcel_Chart_Legend;

        foreach ($positionValues as $positionValue) {
            $result = $testInstance->setPosition($positionValue);
            $this->assertTrue($result);
        }
    }

    public function test_set_invalid_position_returns_false()
    {
        $testInstance = new PHPExcel_Chart_Legend;

        $result = $testInstance->setPosition('BottomLeft');
        $this->assertFalse($result);
        //	Ensure that value is unchanged
        $result = $testInstance->getPosition();
        $this->assertEquals(PHPExcel_Chart_Legend::POSITION_RIGHT, $result);
    }

    public function test_get_position()
    {
        $PositionValue = PHPExcel_Chart_Legend::POSITION_BOTTOM;

        $testInstance = new PHPExcel_Chart_Legend;
        $setValue = $testInstance->setPosition($PositionValue);

        $result = $testInstance->getPosition();
        $this->assertEquals($PositionValue, $result);
    }

    public function test_set_position_xl()
    {
        $positionValues = [
            PHPExcel_Chart_Legend::xlLegendPositionBottom,
            PHPExcel_Chart_Legend::xlLegendPositionCorner,
            PHPExcel_Chart_Legend::xlLegendPositionCustom,
            PHPExcel_Chart_Legend::xlLegendPositionLeft,
            PHPExcel_Chart_Legend::xlLegendPositionRight,
            PHPExcel_Chart_Legend::xlLegendPositionTop,
        ];

        $testInstance = new PHPExcel_Chart_Legend;

        foreach ($positionValues as $positionValue) {
            $result = $testInstance->setPositionXL($positionValue);
            $this->assertTrue($result);
        }
    }

    public function test_set_invalid_xl_position_returns_false()
    {
        $testInstance = new PHPExcel_Chart_Legend;

        $result = $testInstance->setPositionXL(999);
        $this->assertFalse($result);
        //	Ensure that value is unchanged
        $result = $testInstance->getPositionXL();
        $this->assertEquals(PHPExcel_Chart_Legend::xlLegendPositionRight, $result);
    }

    public function test_get_position_xl()
    {
        $PositionValue = PHPExcel_Chart_Legend::xlLegendPositionCorner;

        $testInstance = new PHPExcel_Chart_Legend;
        $setValue = $testInstance->setPositionXL($PositionValue);

        $result = $testInstance->getPositionXL();
        $this->assertEquals($PositionValue, $result);
    }

    public function test_set_overlay()
    {
        $overlayValues = [
            true,
            false,
        ];

        $testInstance = new PHPExcel_Chart_Legend;

        foreach ($overlayValues as $overlayValue) {
            $result = $testInstance->setOverlay($overlayValue);
            $this->assertTrue($result);
        }
    }

    public function test_set_invalid_overlay_returns_false()
    {
        $testInstance = new PHPExcel_Chart_Legend;

        $result = $testInstance->setOverlay('INVALID');
        $this->assertFalse($result);

        $result = $testInstance->getOverlay();
        $this->assertFalse($result);
    }

    public function test_get_overlay()
    {
        $OverlayValue = true;

        $testInstance = new PHPExcel_Chart_Legend;
        $setValue = $testInstance->setOverlay($OverlayValue);

        $result = $testInstance->getOverlay();
        $this->assertEquals($OverlayValue, $result);
    }
}
