<?php

namespace Mockery\Generator\StringManipulation\Pass;

use Mockery\Generator\MockConfiguration;

class ClassNamePassTest extends \PHPUnit_Framework_TestCase
{
    const CODE = 'namespace Mockery; class Mock {}';

    protected function setup()
    {
        $this->pass = new ClassNamePass;
    }

    /**
     * @test
     */
    public function should_remove_namespace_definition()
    {
        $config = new MockConfiguration([], [], [], "Dave\Dave");
        $code = $this->pass->apply(static::CODE, $config);
        $this->assertNotContains('namespace Mockery;', $code);
    }

    /**
     * @test
     */
    public function should_replace_namespace_if_class_name_is_namespaced()
    {
        $config = new MockConfiguration([], [], [], "Dave\Dave");
        $code = $this->pass->apply(static::CODE, $config);
        $this->assertNotContains('namespace Mockery;', $code);
        $this->assertContains('namespace Dave;', $code);
    }

    /**
     * @test
     */
    public function should_replace_class_name_with_specified_name()
    {
        $config = new MockConfiguration([], [], [], 'Dave');
        $code = $this->pass->apply(static::CODE, $config);
        $this->assertContains('class Dave', $code);
    }

    /**
     * @test
     */
    public function should_remove_leading_backslashes_from_namespace()
    {
        $config = new MockConfiguration([], [], [], "\Dave\Dave");
        $code = $this->pass->apply(static::CODE, $config);
        $this->assertContains('namespace Dave;', $code);
    }
}
