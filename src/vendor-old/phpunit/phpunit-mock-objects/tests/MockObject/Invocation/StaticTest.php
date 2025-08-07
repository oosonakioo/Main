<?php

class Framework_MockObject_Invocation_StaticTest extends PHPUnit_Framework_TestCase
{
    public function test_constructor_requires_class_and_method_and_parameters()
    {
        new PHPUnit_Framework_MockObject_Invocation_Static('FooClass', 'FooMethod', ['an_argument']);
    }

    public function test_allow_to_get_class_name_set_in_constructor()
    {
        $invocation = new PHPUnit_Framework_MockObject_Invocation_Static('FooClass', 'FooMethod', ['an_argument']);

        $this->assertSame('FooClass', $invocation->className);
    }

    public function test_allow_to_get_method_name_set_in_constructor()
    {
        $invocation = new PHPUnit_Framework_MockObject_Invocation_Static('FooClass', 'FooMethod', ['an_argument']);

        $this->assertSame('FooMethod', $invocation->methodName);
    }

    public function test_allow_to_get_method_parameters_set_in_constructor()
    {
        $expectedParameters = [
            'foo', 5, ['a', 'b'], new StdClass, null, false,
        ];

        $invocation = new PHPUnit_Framework_MockObject_Invocation_Static(
            'FooClass',
            'FooMethod',
            $expectedParameters
        );

        $this->assertSame($expectedParameters, $invocation->parameters);
    }

    public function test_constructor_allow_to_set_flag_clone_objects_in_parameters()
    {
        $parameters = [new StdClass];
        $cloneObjects = true;

        $invocation = new PHPUnit_Framework_MockObject_Invocation_Static(
            'FooClass',
            'FooMethod',
            $parameters,
            $cloneObjects
        );

        $this->assertEquals($parameters, $invocation->parameters);
        $this->assertNotSame($parameters, $invocation->parameters);
    }
}
