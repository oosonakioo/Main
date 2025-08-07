<?php

namespace Mockery\Test\Generator\StringManipulation\Pass;

use Mockery as m;
use Mockery\Generator\StringManipulation\Pass\InterfacePass;

class InterfacePassTest extends \PHPUnit_Framework_TestCase
{
    const CODE = 'class Mock implements MockInterface';

    /**
     * @test
     */
    public function should_not_alter_code_if_no_target_interfaces()
    {
        $pass = new InterfacePass;

        $config = m::mock("Mockery\Generator\MockConfiguration", [
            'getTargetInterfaces' => [],
        ]);

        $code = $pass->apply(static::CODE, $config);
        $this->assertEquals(static::CODE, $code);
    }

    /**
     * @test
     */
    public function should_add_any_interface_names_to_implements_definition()
    {
        $pass = new InterfacePass;

        $config = m::mock("Mockery\Generator\MockConfiguration", [
            'getTargetInterfaces' => [
                m::mock(['getName' => "Dave\Dave"]),
                m::mock(['getName' => "Paddy\Paddy"]),
            ],
        ]);

        $code = $pass->apply(static::CODE, $config);

        $this->assertContains("implements MockInterface, \Dave\Dave, \Paddy\Paddy", $code);
    }
}
