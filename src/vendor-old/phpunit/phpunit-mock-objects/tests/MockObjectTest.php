<?php

/*
 * This file is part of the PHPUnit_MockObject package.
 *
 * (c) Sebastian Bergmann <sebastian@phpunit.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * @since      Class available since Release 3.0.0
 */
class Framework_MockObjectTest extends PHPUnit_Framework_TestCase
{
    public function test_mocked_method_is_never_called()
    {
        $mock = $this->getMock('AnInterface');
        $mock->expects($this->never())
            ->method('doSomething');
    }

    public function test_mocked_method_is_never_called_with_parameter()
    {
        $mock = $this->getMock('SomeClass');
        $mock->expects($this->never())
            ->method('doSomething')
            ->with('someArg');
    }

    public function test_mocked_method_is_not_called_when_expects_any_with_parameter()
    {
        $mock = $this->getMock('SomeClass');
        $mock->expects($this->any())
            ->method('doSomethingElse')
            ->with('someArg');
    }

    public function test_mocked_method_is_not_called_when_method_specified_directly_with_parameter()
    {
        $mock = $this->getMock('SomeClass');
        $mock->method('doSomethingElse')
            ->with('someArg');
    }

    public function test_mocked_method_is_called_at_least_once()
    {
        $mock = $this->getMock('AnInterface');
        $mock->expects($this->atLeastOnce())
            ->method('doSomething');

        $mock->doSomething();
    }

    public function test_mocked_method_is_called_at_least_once2()
    {
        $mock = $this->getMock('AnInterface');
        $mock->expects($this->atLeastOnce())
            ->method('doSomething');

        $mock->doSomething();
        $mock->doSomething();
    }

    public function test_mocked_method_is_called_at_least_twice()
    {
        $mock = $this->getMock('AnInterface');
        $mock->expects($this->atLeast(2))
            ->method('doSomething');

        $mock->doSomething();
        $mock->doSomething();
    }

    public function test_mocked_method_is_called_at_least_twice2()
    {
        $mock = $this->getMock('AnInterface');
        $mock->expects($this->atLeast(2))
            ->method('doSomething');

        $mock->doSomething();
        $mock->doSomething();
        $mock->doSomething();
    }

    public function test_mocked_method_is_called_at_most_twice()
    {
        $mock = $this->getMock('AnInterface');
        $mock->expects($this->atMost(2))
            ->method('doSomething');

        $mock->doSomething();
        $mock->doSomething();
    }

    public function test_mocked_method_is_called_at_mostt_twice2()
    {
        $mock = $this->getMock('AnInterface');
        $mock->expects($this->atMost(2))
            ->method('doSomething');

        $mock->doSomething();
    }

    public function test_mocked_method_is_called_once()
    {
        $mock = $this->getMock('AnInterface');
        $mock->expects($this->once())
            ->method('doSomething');

        $mock->doSomething();
    }

    public function test_mocked_method_is_called_once_with_parameter()
    {
        $mock = $this->getMock('SomeClass');
        $mock->expects($this->once())
            ->method('doSomethingElse')
            ->with($this->equalTo('something'));

        $mock->doSomethingElse('something');
    }

    public function test_mocked_method_is_called_exactly()
    {
        $mock = $this->getMock('AnInterface');
        $mock->expects($this->exactly(2))
            ->method('doSomething');

        $mock->doSomething();
        $mock->doSomething();
    }

    public function test_stubbed_exception()
    {
        $mock = $this->getMock('AnInterface');
        $mock->expects($this->any())
            ->method('doSomething')
            ->will($this->throwException(new Exception));

        try {
            $mock->doSomething();
        } catch (Exception $e) {
            return;
        }

        $this->fail();
    }

    public function test_stubbed_will_throw_exception()
    {
        $mock = $this->getMock('AnInterface');
        $mock->expects($this->any())
            ->method('doSomething')
            ->willThrowException(new Exception);

        try {
            $mock->doSomething();
        } catch (Exception $e) {
            return;
        }

        $this->fail();
    }

    public function test_stubbed_return_value()
    {
        $mock = $this->getMock('AnInterface');
        $mock->expects($this->any())
            ->method('doSomething')
            ->will($this->returnValue('something'));

        $this->assertEquals('something', $mock->doSomething());

        $mock = $this->getMock('AnInterface');
        $mock->expects($this->any())
            ->method('doSomething')
            ->willReturn('something');

        $this->assertEquals('something', $mock->doSomething());
    }

    public function test_stubbed_return_value_map()
    {
        $map = [
            ['a', 'b', 'c', 'd'],
            ['e', 'f', 'g', 'h'],
        ];

        $mock = $this->getMock('AnInterface');
        $mock->expects($this->any())
            ->method('doSomething')
            ->will($this->returnValueMap($map));

        $this->assertEquals('d', $mock->doSomething('a', 'b', 'c'));
        $this->assertEquals('h', $mock->doSomething('e', 'f', 'g'));
        $this->assertEquals(null, $mock->doSomething('foo', 'bar'));

        $mock = $this->getMock('AnInterface');
        $mock->expects($this->any())
            ->method('doSomething')
            ->willReturnMap($map);

        $this->assertEquals('d', $mock->doSomething('a', 'b', 'c'));
        $this->assertEquals('h', $mock->doSomething('e', 'f', 'g'));
        $this->assertEquals(null, $mock->doSomething('foo', 'bar'));
    }

    public function test_stubbed_return_argument()
    {
        $mock = $this->getMock('AnInterface');
        $mock->expects($this->any())
            ->method('doSomething')
            ->will($this->returnArgument(1));

        $this->assertEquals('b', $mock->doSomething('a', 'b'));

        $mock = $this->getMock('AnInterface');
        $mock->expects($this->any())
            ->method('doSomething')
            ->willReturnArgument(1);

        $this->assertEquals('b', $mock->doSomething('a', 'b'));
    }

    public function test_function_callback()
    {
        $mock = $this->getMock('SomeClass', ['doSomething'], [], '', false);
        $mock->expects($this->once())
            ->method('doSomething')
            ->will($this->returnCallback('functionCallback'));

        $this->assertEquals('pass', $mock->doSomething('foo', 'bar'));

        $mock = $this->getMock('SomeClass', ['doSomething'], [], '', false);
        $mock->expects($this->once())
            ->method('doSomething')
            ->willReturnCallback('functionCallback');

        $this->assertEquals('pass', $mock->doSomething('foo', 'bar'));
    }

    public function test_stubbed_return_self()
    {
        $mock = $this->getMock('AnInterface');
        $mock->expects($this->any())
            ->method('doSomething')
            ->will($this->returnSelf());

        $this->assertEquals($mock, $mock->doSomething());

        $mock = $this->getMock('AnInterface');
        $mock->expects($this->any())
            ->method('doSomething')
            ->willReturnSelf();

        $this->assertEquals($mock, $mock->doSomething());
    }

    public function test_stubbed_return_on_consecutive_calls()
    {
        $mock = $this->getMock('AnInterface');
        $mock->expects($this->any())
            ->method('doSomething')
            ->will($this->onConsecutiveCalls('a', 'b', 'c'));

        $this->assertEquals('a', $mock->doSomething());
        $this->assertEquals('b', $mock->doSomething());
        $this->assertEquals('c', $mock->doSomething());

        $mock = $this->getMock('AnInterface');
        $mock->expects($this->any())
            ->method('doSomething')
            ->willReturnOnConsecutiveCalls('a', 'b', 'c');

        $this->assertEquals('a', $mock->doSomething());
        $this->assertEquals('b', $mock->doSomething());
        $this->assertEquals('c', $mock->doSomething());
    }

    public function test_static_method_callback()
    {
        $mock = $this->getMock('SomeClass', ['doSomething'], [], '', false);
        $mock->expects($this->once())
            ->method('doSomething')
            ->will($this->returnCallback(['MethodCallback', 'staticCallback']));

        $this->assertEquals('pass', $mock->doSomething('foo', 'bar'));
    }

    public function test_public_method_callback()
    {
        $mock = $this->getMock('SomeClass', ['doSomething'], [], '', false);
        $mock->expects($this->once())
            ->method('doSomething')
            ->will($this->returnCallback([new MethodCallback, 'nonStaticCallback']));

        $this->assertEquals('pass', $mock->doSomething('foo', 'bar'));
    }

    public function test_mock_class_only_generated_once()
    {
        $mock1 = $this->getMock('AnInterface');
        $mock2 = $this->getMock('AnInterface');

        $this->assertEquals(get_class($mock1), get_class($mock2));
    }

    public function test_mock_class_different_for_partial_mocks()
    {
        $mock1 = $this->getMock('PartialMockTestClass');
        $mock2 = $this->getMock('PartialMockTestClass', ['doSomething']);
        $mock3 = $this->getMock('PartialMockTestClass', ['doSomething']);
        $mock4 = $this->getMock('PartialMockTestClass', ['doAnotherThing']);
        $mock5 = $this->getMock('PartialMockTestClass', ['doAnotherThing']);

        $this->assertNotEquals(get_class($mock1), get_class($mock2));
        $this->assertNotEquals(get_class($mock1), get_class($mock3));
        $this->assertNotEquals(get_class($mock1), get_class($mock4));
        $this->assertNotEquals(get_class($mock1), get_class($mock5));
        $this->assertEquals(get_class($mock2), get_class($mock3));
        $this->assertNotEquals(get_class($mock2), get_class($mock4));
        $this->assertNotEquals(get_class($mock2), get_class($mock5));
        $this->assertEquals(get_class($mock4), get_class($mock5));
    }

    public function test_mock_class_store_overrulable()
    {
        $mock1 = $this->getMock('PartialMockTestClass');
        $mock2 = $this->getMock('PartialMockTestClass', [], [], 'MyMockClassNameForPartialMockTestClass1');
        $mock3 = $this->getMock('PartialMockTestClass');
        $mock4 = $this->getMock('PartialMockTestClass', ['doSomething'], [], 'AnotherMockClassNameForPartialMockTestClass');
        $mock5 = $this->getMock('PartialMockTestClass', [], [], 'MyMockClassNameForPartialMockTestClass2');

        $this->assertNotEquals(get_class($mock1), get_class($mock2));
        $this->assertEquals(get_class($mock1), get_class($mock3));
        $this->assertNotEquals(get_class($mock1), get_class($mock4));
        $this->assertNotEquals(get_class($mock2), get_class($mock3));
        $this->assertNotEquals(get_class($mock2), get_class($mock4));
        $this->assertNotEquals(get_class($mock2), get_class($mock5));
        $this->assertNotEquals(get_class($mock3), get_class($mock4));
        $this->assertNotEquals(get_class($mock3), get_class($mock5));
        $this->assertNotEquals(get_class($mock4), get_class($mock5));
    }

    /**
     * @covers PHPUnit_Framework_MockObject_Generator::getMock
     */
    public function test_get_mock_with_fixed_class_name_can_produce_the_same_mock_twice()
    {
        $mock = $this->getMockBuilder('StdClass')->setMockClassName('FixedName')->getMock();
        $mock = $this->getMockBuilder('StdClass')->setMockClassName('FixedName')->getMock();
        $this->assertInstanceOf('StdClass', $mock);
    }

    public function test_original_constructor_setting_considered()
    {
        $mock1 = $this->getMock('PartialMockTestClass');
        $mock2 = $this->getMock('PartialMockTestClass', [], [], '', false);

        $this->assertTrue($mock1->constructorCalled);
        $this->assertFalse($mock2->constructorCalled);
    }

    public function test_original_clone_setting_considered()
    {
        $mock1 = $this->getMock('PartialMockTestClass');
        $mock2 = $this->getMock('PartialMockTestClass', [], [], '', true, false);

        $this->assertNotEquals(get_class($mock1), get_class($mock2));
    }

    public function test_get_mock_for_abstract_class()
    {
        $mock = $this->getMock('AbstractMockTestClass');
        $mock->expects($this->never())
            ->method('doSomething');
    }

    public function traversableProvider()
    {
        return [
            ['Traversable'],
            ['\Traversable'],
            ['TraversableMockTestInterface'],
            [['Traversable']],
            [['Iterator', 'Traversable']],
            [['\Iterator', '\Traversable']],
        ];
    }

    /**
     * @dataProvider traversableProvider
     */
    public function test_get_mock_for_traversable($type)
    {
        $mock = $this->getMock($type);
        $this->assertInstanceOf('Traversable', $mock);
    }

    public function test_multiple_interfaces_can_be_mocked_in_single_object()
    {
        $mock = $this->getMock(['AnInterface', 'AnotherInterface']);
        $this->assertInstanceOf('AnInterface', $mock);
        $this->assertInstanceOf('AnotherInterface', $mock);
    }

    /**
     * @requires PHP 5.4.0
     */
    public function test_get_mock_for_trait()
    {
        $mock = $this->getMockForTrait('AbstractTrait');
        $mock->expects($this->never())->method('doSomething');

        $parent = get_parent_class($mock);
        $traits = class_uses($parent, false);

        $this->assertContains('AbstractTrait', $traits);
    }

    public function test_cloned_mock_object_should_still_equal_the_original()
    {
        $a = $this->getMock('stdClass');
        $b = clone $a;
        $this->assertEquals($a, $b);
    }

    public function test_mock_objects_constructed_indepentantly_should_be_equal()
    {
        $a = $this->getMock('stdClass');
        $b = $this->getMock('stdClass');
        $this->assertEquals($a, $b);
    }

    public function test_mock_objects_constructed_indepentantly_should_not_be_the_same()
    {
        $a = $this->getMock('stdClass');
        $b = $this->getMock('stdClass');
        $this->assertNotSame($a, $b);
    }

    public function test_cloned_mock_object_can_be_used_in_place_of_original_one()
    {
        $x = $this->getMock('stdClass');
        $y = clone $x;

        $mock = $this->getMock('stdClass', ['foo']);
        $mock->expects($this->once())->method('foo')->with($this->equalTo($x));
        $mock->foo($y);
    }

    public function test_cloned_mock_object_is_not_identical_to_original_one()
    {
        $x = $this->getMock('stdClass');
        $y = clone $x;

        $mock = $this->getMock('stdClass', ['foo']);
        $mock->expects($this->once())->method('foo')->with($this->logicalNot($this->identicalTo($x)));
        $mock->foo($y);
    }

    public function test_object_method_call_with_argument_cloning_enabled()
    {
        $expectedObject = new StdClass;

        $mock = $this->getMockBuilder('SomeClass')
            ->setMethods(['doSomethingElse'])
            ->enableArgumentCloning()
            ->getMock();

        $actualArguments = [];

        $mock->expects($this->any())
            ->method('doSomethingElse')
            ->will($this->returnCallback(function () use (&$actualArguments) {
                $actualArguments = func_get_args();
            }));

        $mock->doSomethingElse($expectedObject);

        $this->assertEquals(1, count($actualArguments));
        $this->assertEquals($expectedObject, $actualArguments[0]);
        $this->assertNotSame($expectedObject, $actualArguments[0]);
    }

    public function test_object_method_call_with_argument_cloning_disabled()
    {
        $expectedObject = new StdClass;

        $mock = $this->getMockBuilder('SomeClass')
            ->setMethods(['doSomethingElse'])
            ->disableArgumentCloning()
            ->getMock();

        $actualArguments = [];

        $mock->expects($this->any())
            ->method('doSomethingElse')
            ->will($this->returnCallback(function () use (&$actualArguments) {
                $actualArguments = func_get_args();
            }));

        $mock->doSomethingElse($expectedObject);

        $this->assertEquals(1, count($actualArguments));
        $this->assertSame($expectedObject, $actualArguments[0]);
    }

    public function test_argument_cloning_option_generates_unique_mock()
    {
        $mockWithCloning = $this->getMockBuilder('SomeClass')
            ->setMethods(['doSomethingElse'])
            ->enableArgumentCloning()
            ->getMock();

        $mockWithoutCloning = $this->getMockBuilder('SomeClass')
            ->setMethods(['doSomethingElse'])
            ->disableArgumentCloning()
            ->getMock();

        $this->assertNotEquals($mockWithCloning, $mockWithoutCloning);
    }

    public function test_verification_of_method_name_fails_without_parameters()
    {
        $mock = $this->getMock('SomeClass', ['right', 'wrong'], [], '', true, true, true);
        $mock->expects($this->once())
            ->method('right');

        $mock->wrong();
        try {
            $mock->__phpunit_verify();
            $this->fail('Expected exception');
        } catch (PHPUnit_Framework_ExpectationFailedException $e) {
            $this->assertSame(
                "Expectation failed for method name is equal to <string:right> when invoked 1 time(s).\n"
                ."Method was expected to be called 1 times, actually called 0 times.\n",
                $e->getMessage()
            );
        }

        $this->resetMockObjects();
    }

    public function test_verification_of_method_name_fails_with_parameters()
    {
        $mock = $this->getMock('SomeClass', ['right', 'wrong'], [], '', true, true, true);
        $mock->expects($this->once())
            ->method('right');

        $mock->wrong();
        try {
            $mock->__phpunit_verify();
            $this->fail('Expected exception');
        } catch (PHPUnit_Framework_ExpectationFailedException $e) {
            $this->assertSame(
                "Expectation failed for method name is equal to <string:right> when invoked 1 time(s).\n"
                ."Method was expected to be called 1 times, actually called 0 times.\n",
                $e->getMessage()
            );
        }

        $this->resetMockObjects();
    }

    public function test_verification_of_method_name_fails_with_wrong_parameters()
    {
        $mock = $this->getMock('SomeClass', ['right', 'wrong'], [], '', true, true, true);
        $mock->expects($this->once())
            ->method('right')
            ->with(['first', 'second']);

        try {
            $mock->right(['second']);
        } catch (PHPUnit_Framework_ExpectationFailedException $e) {
            $this->assertSame(
                "Expectation failed for method name is equal to <string:right> when invoked 1 time(s)\n"
                ."Parameter 0 for invocation SomeClass::right(Array (...)) does not match expected value.\n"
                .'Failed asserting that two arrays are equal.',
                $e->getMessage()
            );
        }

        try {
            $mock->__phpunit_verify();
            $this->fail('Expected exception');
        } catch (PHPUnit_Framework_ExpectationFailedException $e) {
            $this->assertSame(
                "Expectation failed for method name is equal to <string:right> when invoked 1 time(s).\n"
                ."Parameter 0 for invocation SomeClass::right(Array (...)) does not match expected value.\n"
                ."Failed asserting that two arrays are equal.\n"
                ."--- Expected\n"
                ."+++ Actual\n"
                ."@@ @@\n"
                ." Array (\n"
                ."-    0 => 'first'\n"
                ."-    1 => 'second'\n"
                ."+    0 => 'second'\n"
                ." )\n",
                $e->getMessage()
            );
        }

        $this->resetMockObjects();
    }

    public function test_verification_of_never_fails_with_empty_parameters()
    {
        $mock = $this->getMock('SomeClass', ['right', 'wrong'], [], '', true, true, true);
        $mock->expects($this->never())
            ->method('right')
            ->with();

        try {
            $mock->right();
            $this->fail('Expected exception');
        } catch (PHPUnit_Framework_ExpectationFailedException $e) {
            $this->assertSame(
                'SomeClass::right() was not expected to be called.',
                $e->getMessage()
            );
        }

        $this->resetMockObjects();
    }

    public function test_verification_of_never_fails_with_any_parameters()
    {
        $mock = $this->getMock('SomeClass', ['right', 'wrong'], [], '', true, true, true);
        $mock->expects($this->never())
            ->method('right')
            ->withAnyParameters();

        try {
            $mock->right();
            $this->fail('Expected exception');
        } catch (PHPUnit_Framework_ExpectationFailedException $e) {
            $this->assertSame(
                'SomeClass::right() was not expected to be called.',
                $e->getMessage()
            );
        }

        $this->resetMockObjects();
    }

    /**
     * @ticket 199
     */
    public function test_with_anything_instead_of_with_any_parameters()
    {
        $mock = $this->getMock('SomeClass', ['right'], [], '', true, true, true);
        $mock->expects($this->once())
            ->method('right')
            ->with($this->anything());

        try {
            $mock->right();
            $this->fail('Expected exception');
        } catch (PHPUnit_Framework_ExpectationFailedException $e) {
            $this->assertSame(
                "Expectation failed for method name is equal to <string:right> when invoked 1 time(s)\n".
                "Parameter count for invocation SomeClass::right() is too low.\n".
                'To allow 0 or more parameters with any value, omit ->with() or use ->withAnyParameters() instead.',
                $e->getMessage()
            );
        }

        $this->resetMockObjects();
    }

    /**
     * See https://github.com/sebastianbergmann/phpunit-mock-objects/issues/81
     */
    public function test_mock_arguments_passed_by_reference()
    {
        $foo = $this->getMockBuilder('MethodCallbackByReference')
            ->setMethods(['bar'])
            ->disableOriginalConstructor()
            ->disableArgumentCloning()
            ->getMock();

        $foo->expects($this->any())
            ->method('bar')
            ->will($this->returnCallback([$foo, 'callback']));

        $a = $b = $c = 0;

        $foo->bar($a, $b, $c);

        $this->assertEquals(1, $b);
    }

    /**
     * See https://github.com/sebastianbergmann/phpunit-mock-objects/issues/81
     */
    public function test_mock_arguments_passed_by_reference2()
    {
        $foo = $this->getMockBuilder('MethodCallbackByReference')
            ->disableOriginalConstructor()
            ->disableArgumentCloning()
            ->getMock();

        $foo->expects($this->any())
            ->method('bar')
            ->will($this->returnCallback(
                function (&$a, &$b, $c) {
                    $b = 1;
                }
            ));

        $a = $b = $c = 0;

        $foo->bar($a, $b, $c);

        $this->assertEquals(1, $b);
    }

    /**
     * https://github.com/sebastianbergmann/phpunit-mock-objects/issues/116
     */
    public function test_mock_arguments_passed_by_reference3()
    {
        $foo = $this->getMockBuilder('MethodCallbackByReference')
            ->setMethods(['bar'])
            ->disableOriginalConstructor()
            ->disableArgumentCloning()
            ->getMock();

        $a = new stdClass;
        $b = $c = 0;

        $foo->expects($this->any())
            ->method('bar')
            ->with($a, $b, $c)
            ->will($this->returnCallback([$foo, 'callback']));

        $foo->bar($a, $b, $c);
    }

    /**
     * https://github.com/sebastianbergmann/phpunit/issues/796
     */
    public function test_mock_arguments_passed_by_reference4()
    {
        $foo = $this->getMockBuilder('MethodCallbackByReference')
            ->setMethods(['bar'])
            ->disableOriginalConstructor()
            ->disableArgumentCloning()
            ->getMock();

        $a = new stdClass;
        $b = $c = 0;

        $foo->expects($this->any())
            ->method('bar')
            ->with($this->isInstanceOf('stdClass'), $b, $c)
            ->will($this->returnCallback([$foo, 'callback']));

        $foo->bar($a, $b, $c);
    }

    /**
     * @requires extension soap
     */
    public function test_create_mock_from_wsdl()
    {
        $mock = $this->getMockFromWsdl(__DIR__.'/_fixture/GoogleSearch.wsdl', 'WsdlMock');
        $this->assertStringStartsWith(
            'Mock_WsdlMock_',
            get_class($mock)
        );
    }

    /**
     * @requires extension soap
     */
    public function test_create_namespaced_mock_from_wsdl()
    {
        $mock = $this->getMockFromWsdl(__DIR__.'/_fixture/GoogleSearch.wsdl', 'My\\Space\\WsdlMock');
        $this->assertStringStartsWith(
            'Mock_WsdlMock_',
            get_class($mock)
        );
    }

    /**
     * @requires extension soap
     */
    public function test_create_two_mocks_of_one_wsdl_file()
    {
        $mock = $this->getMockFromWsdl(__DIR__.'/_fixture/GoogleSearch.wsdl');
        $mock = $this->getMockFromWsdl(__DIR__.'/_fixture/GoogleSearch.wsdl');
    }

    /**
     * @see    https://github.com/sebastianbergmann/phpunit-mock-objects/issues/156
     *
     * @ticket 156
     */
    public function test_interface_with_static_method_can_be_stubbed()
    {
        $this->assertInstanceOf(
            'InterfaceWithStaticMethod',
            $this->getMock('InterfaceWithStaticMethod')
        );
    }

    /**
     * @expectedException PHPUnit_Framework_MockObject_BadMethodCallException
     */
    public function test_invoking_stubbed_static_method_raises_exception()
    {
        $mock = $this->getMock('ClassWithStaticMethod');
        $mock->staticMethod();
    }

    /**
     * @see    https://github.com/sebastianbergmann/phpunit-mock-objects/issues/171
     *
     * @ticket 171
     */
    public function test_stub_for_class_that_implements_serializable_can_be_created_without_invoking_the_constructor()
    {
        $this->assertInstanceOf(
            'ClassThatImplementsSerializable',
            $this->getMockBuilder('ClassThatImplementsSerializable')
                ->disableOriginalConstructor()
                ->getMock()
        );
    }

    private function resetMockObjects()
    {
        $refl = new ReflectionObject($this);
        $refl = $refl->getParentClass();
        $prop = $refl->getProperty('mockObjects');
        $prop->setAccessible(true);
        $prop->setValue($this, []);
    }
}
