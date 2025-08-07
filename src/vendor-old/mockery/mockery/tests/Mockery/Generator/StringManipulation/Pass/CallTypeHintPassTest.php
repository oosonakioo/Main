<?php

namespace Mockery\Test\Generator\StringManipulation\Pass;

use Mockery as m;
use Mockery\Generator\StringManipulation\Pass\CallTypeHintPass;

class CallTypeHintPassTest extends \PHPUnit_Framework_TestCase
{
    const CODE = ' public function __call($method, array $args) {}
                   public static function __callStatic($method, array $args) {}
    ';

    /**
     * @test
     */
    public function should_remove_call_type_hint_if_required()
    {
        $pass = new CallTypeHintPass;
        $config = m::mock("Mockery\Generator\MockConfiguration", [
            'requiresCallTypeHintRemoval' => true,
        ])->shouldDeferMissing();
        $code = $pass->apply(static::CODE, $config);
        $this->assertContains('__call($method, $args)', $code);
    }

    /**
     * @test
     */
    public function should_remove_call_static_type_hint_if_required()
    {
        $pass = new CallTypeHintPass;
        $config = m::mock("Mockery\Generator\MockConfiguration", [
            'requiresCallStaticTypeHintRemoval' => true,
        ])->shouldDeferMissing();
        $code = $pass->apply(static::CODE, $config);
        $this->assertContains('__callStatic($method, $args)', $code);
    }
}
