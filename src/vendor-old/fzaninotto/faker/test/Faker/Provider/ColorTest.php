<?php

namespace Faker\Test\Provider;

use Faker\Provider\Color;

class ColorTest extends \PHPUnit_Framework_TestCase
{
    public function test_hex_color()
    {
        $this->assertRegExp('/^#[a-f0-9]{6}$/i', Color::hexColor());
    }

    public function test_safe_hex_color()
    {
        $this->assertRegExp('/^#[a-f0-9]{6}$/i', Color::safeHexColor());
    }

    public function test_rgb_color_as_array()
    {
        $this->assertEquals(3, count(Color::rgbColorAsArray()));
    }

    public function test_rgb_color()
    {
        $regexp = '([01]?[0-9]?[0-9]|2[0-4][0-9]|25[0-5])';
        $this->assertRegExp('/^'.$regexp.','.$regexp.','.$regexp.'$/i', Color::rgbColor());
    }

    public function test_rgb_css_color()
    {
        $regexp = '([01]?[0-9]?[0-9]|2[0-4][0-9]|25[0-5])';
        $this->assertRegExp('/^rgb\('.$regexp.','.$regexp.','.$regexp.'\)$/i', Color::rgbCssColor());
    }

    public function test_rgba_css_color()
    {
        $regexp = '([01]?[0-9]?[0-9]|2[0-4][0-9]|25[0-5])';
        $regexpAlpha = '([01]?(\.\d+)?)';
        $this->assertRegExp('/^rgba\('.$regexp.','.$regexp.','.$regexp.','.$regexpAlpha.'\)$/i', Color::rgbaCssColor());
    }

    public function test_safe_color_name()
    {
        $this->assertRegExp('/^[\w]+$/', Color::safeColorName());
    }

    public function test_color_name()
    {
        $this->assertRegExp('/^[\w]+$/', Color::colorName());
    }
}
