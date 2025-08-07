<?php

use JakubOnderka\PhpConsoleColor\ConsoleColor;

class ConsoleColorWithForceSupport extends ConsoleColor
{
    private $isSupportedForce = true;

    private $are256ColorsSupportedForce = true;

    public function setIsSupported($isSupported)
    {
        $this->isSupportedForce = $isSupported;
    }

    public function isSupported()
    {
        return $this->isSupportedForce;
    }

    public function setAre256ColorsSupported($are256ColorsSupported)
    {
        $this->are256ColorsSupportedForce = $are256ColorsSupported;
    }

    public function are256ColorsSupported()
    {
        return $this->are256ColorsSupportedForce;
    }
}

class ConsoleColorTest extends \PHPUnit_Framework_TestCase
{
    /** @var ConsoleColorWithForceSupport */
    private $uut;

    protected function setUp()
    {
        $this->uut = new ConsoleColorWithForceSupport;
    }

    public function test_none()
    {
        $output = $this->uut->apply('none', 'text');
        $this->assertEquals('text', $output);
    }

    public function test_bold()
    {
        $output = $this->uut->apply('bold', 'text');
        $this->assertEquals("\033[1mtext\033[0m", $output);
    }

    public function test_bold_colors_are_not_supported()
    {
        $this->uut->setIsSupported(false);

        $output = $this->uut->apply('bold', 'text');
        $this->assertEquals('text', $output);
    }

    public function test_bold_colors_are_not_supported_but_are_forced()
    {
        $this->uut->setIsSupported(false);
        $this->uut->setForceStyle(true);

        $output = $this->uut->apply('bold', 'text');
        $this->assertEquals("\033[1mtext\033[0m", $output);
    }

    public function test_dark()
    {
        $output = $this->uut->apply('dark', 'text');
        $this->assertEquals("\033[2mtext\033[0m", $output);
    }

    public function test_bold_and_dark()
    {
        $output = $this->uut->apply(['bold', 'dark'], 'text');
        $this->assertEquals("\033[1;2mtext\033[0m", $output);
    }

    public function test256_color_foreground()
    {
        $output = $this->uut->apply('color_255', 'text');
        $this->assertEquals("\033[38;5;255mtext\033[0m", $output);
    }

    public function test256_color_without_support()
    {
        $this->uut->setAre256ColorsSupported(false);

        $output = $this->uut->apply('color_255', 'text');
        $this->assertEquals('text', $output);
    }

    public function test256_color_background()
    {
        $output = $this->uut->apply('bg_color_255', 'text');
        $this->assertEquals("\033[48;5;255mtext\033[0m", $output);
    }

    public function test256_color_foreground_and_background()
    {
        $output = $this->uut->apply(['color_200', 'bg_color_255'], 'text');
        $this->assertEquals("\033[38;5;200;48;5;255mtext\033[0m", $output);
    }

    public function test_set_own_theme()
    {
        $this->uut->setThemes(['bold_dark' => ['bold', 'dark']]);
        $output = $this->uut->apply(['bold_dark'], 'text');
        $this->assertEquals("\033[1;2mtext\033[0m", $output);
    }

    public function test_add_own_theme()
    {
        $this->uut->addTheme('bold_own', 'bold');
        $output = $this->uut->apply(['bold_own'], 'text');
        $this->assertEquals("\033[1mtext\033[0m", $output);
    }

    public function test_add_own_theme_array()
    {
        $this->uut->addTheme('bold_dark', ['bold', 'dark']);
        $output = $this->uut->apply(['bold_dark'], 'text');
        $this->assertEquals("\033[1;2mtext\033[0m", $output);
    }

    public function test_own_with_style()
    {
        $this->uut->addTheme('bold_dark', ['bold', 'dark']);
        $output = $this->uut->apply(['bold_dark', 'italic'], 'text');
        $this->assertEquals("\033[1;2;3mtext\033[0m", $output);
    }

    public function test_has_and_remove_theme()
    {
        $this->assertFalse($this->uut->hasTheme('bold_dark'));

        $this->uut->addTheme('bold_dark', ['bold', 'dark']);
        $this->assertTrue($this->uut->hasTheme('bold_dark'));

        $this->uut->removeTheme('bold_dark');
        $this->assertFalse($this->uut->hasTheme('bold_dark'));
    }

    public function test_apply_invalid_argument()
    {
        $this->setExpectedException('\InvalidArgumentException');
        $this->uut->apply(new stdClass, 'text');
    }

    public function test_apply_invalid_style_name()
    {
        $this->setExpectedException('\JakubOnderka\PhpConsoleColor\InvalidStyleException');
        $this->uut->apply('invalid', 'text');
    }

    public function test_apply_invalid256_color()
    {
        $this->setExpectedException('\JakubOnderka\PhpConsoleColor\InvalidStyleException');
        $this->uut->apply('color_2134', 'text');
    }

    public function test_theme_invalid_style()
    {
        $this->setExpectedException('\JakubOnderka\PhpConsoleColor\InvalidStyleException');
        $this->uut->addTheme('invalid', ['invalid']);
    }

    public function test_force_style()
    {
        $this->assertFalse($this->uut->isStyleForced());
        $this->uut->setForceStyle(true);
        $this->assertTrue($this->uut->isStyleForced());
    }

    public function test_get_possible_styles()
    {
        $this->assertInternalType('array', $this->uut->getPossibleStyles());
        $this->assertNotEmpty($this->uut->getPossibleStyles());
    }
}
