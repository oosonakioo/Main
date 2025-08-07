<?php

/**
 * Mockery
 *
 * LICENSE
 *
 * This source file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://github.com/padraic/mockery/master/LICENSE
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to padraic@php.net so we can send you a copy immediately.
 *
 * @category   Mockery
 *
 * @copyright  Copyright (c) 2010-2014 Pádraic Brady (http://blog.astrumfutura.com)
 * @license    http://github.com/padraic/mockery/blob/master/LICENSE New BSD License
 */

use Mockery\Adapter\Phpunit\MockeryTestCase;
use Mockery\Generator\MockConfigurationBuilder;

class ContainerTest extends MockeryTestCase
{
    /** @var Mockery\Container */
    private $container;

    protected function setup()
    {
        $this->container = new Mockery\Container(Mockery::getDefaultGenerator(), new Mockery\Loader\EvalLoader);
    }

    protected function teardown()
    {
        $this->container->mockery_close();
    }

    public function test_simplest_mock_creation()
    {
        $m = $this->container->mock();
        $m->shouldReceive('foo')->andReturn('bar');
        $this->assertEquals('bar', $m->foo());
    }

    public function test_get_key_of_demeter_mock_should_return_key_when_matching_mock()
    {
        $m = $this->container->mock();
        $m->shouldReceive('foo->bar');
        $this->assertRegExp(
            '/Mockery_(\d+)__demeter_foo/',
            $this->container->getKeyOfDemeterMockFor('foo')
        );
    }

    public function test_get_key_of_demeter_mock_should_return_null_when_no_matching_mock()
    {
        $method = 'unknownMethod';
        $this->assertNull($this->container->getKeyOfDemeterMockFor($method));

        $m = $this->container->mock();
        $m->shouldReceive('method');
        $this->assertNull($this->container->getKeyOfDemeterMockFor($method));

        $m->shouldReceive('foo->bar');
        $this->assertNull($this->container->getKeyOfDemeterMockFor($method));
    }

    public function test_named_mocks_add_name_to_exceptions()
    {
        $m = $this->container->mock('Foo');
        $m->shouldReceive('foo')->with(1)->andReturn('bar');
        try {
            $m->foo();
        } catch (\Mockery\Exception $e) {
            $this->assertTrue((bool) preg_match('/Foo/', $e->getMessage()));
        }
    }

    public function test_simple_mock_with_array_defs()
    {
        $m = $this->container->mock(['foo' => 1, 'bar' => 2]);
        $this->assertEquals(1, $m->foo());
        $this->assertEquals(2, $m->bar());
    }

    public function test_simple_mock_with_array_defs_can_be_overridden()
    {
        // eg. In shared test setup
        $m = $this->container->mock(['foo' => 1, 'bar' => 2]);

        // and then overridden in one test
        $m->shouldReceive('foo')->with('baz')->once()->andReturn(2);
        $m->shouldReceive('bar')->with('baz')->once()->andReturn(42);

        $this->assertEquals(2, $m->foo('baz'));
        $this->assertEquals(42, $m->bar('baz'));
    }

    public function test_named_mock_with_array_defs()
    {
        $m = $this->container->mock('Foo', ['foo' => 1, 'bar' => 2]);
        $this->assertEquals(1, $m->foo());
        $this->assertEquals(2, $m->bar());
        try {
            $m->f();
        } catch (BadMethodCallException $e) {
            $this->assertTrue((bool) preg_match('/Foo/', $e->getMessage()));
        }
    }

    public function test_named_mock_with_array_defs_can_be_overridden()
    {
        // eg. In shared test setup
        $m = $this->container->mock('Foo', ['foo' => 1]);

        // and then overridden in one test
        $m->shouldReceive('foo')->with('bar')->once()->andReturn(2);

        $this->assertEquals(2, $m->foo('bar'));

        try {
            $m->f();
        } catch (BadMethodCallException $e) {
            $this->assertTrue((bool) preg_match('/Foo/', $e->getMessage()));
        }
    }

    public function test_named_mock_multiple_interfaces()
    {
        $m = $this->container->mock('stdClass, ArrayAccess, Countable', ['foo' => 1, 'bar' => 2]);
        $this->assertEquals(1, $m->foo());
        $this->assertEquals(2, $m->bar());
        try {
            $m->f();
        } catch (BadMethodCallException $e) {
            $this->assertTrue((bool) preg_match('/stdClass/', $e->getMessage()));
            $this->assertTrue((bool) preg_match('/ArrayAccess/', $e->getMessage()));
            $this->assertTrue((bool) preg_match('/Countable/', $e->getMessage()));
        }
    }

    public function test_named_mock_with_constructor_args()
    {
        $m = $this->container->mock('MockeryTest_ClassConstructor2[foo]', [$param1 = new stdClass]);
        $m->shouldReceive('foo')->andReturn(123);
        $this->assertEquals(123, $m->foo());
        $this->assertEquals($param1, $m->getParam1());
    }

    public function test_named_mock_with_constructor_args_and_array_defs()
    {
        $m = $this->container->mock(
            'MockeryTest_ClassConstructor2[foo]',
            [$param1 = new stdClass],
            ['foo' => 123]
        );
        $this->assertEquals(123, $m->foo());
        $this->assertEquals($param1, $m->getParam1());
    }

    public function test_named_mock_with_constructor_args_with_internal_call_to_mocked_method()
    {
        $m = $this->container->mock('MockeryTest_ClassConstructor2[foo]', [$param1 = new stdClass]);
        $m->shouldReceive('foo')->andReturn(123);
        $this->assertEquals(123, $m->bar());
    }

    public function test_named_mock_with_constructor_args_but_no_quick_defs_should_leave_constructor_intact()
    {
        $m = $this->container->mock('MockeryTest_ClassConstructor2', [$param1 = new stdClass]);
        $m->shouldDeferMissing();
        $this->assertEquals($param1, $m->getParam1());
    }

    public function test_named_mock_with_should_defer_missing()
    {
        $m = $this->container->mock('MockeryTest_ClassConstructor2', [$param1 = new stdClass]);
        $m->shouldDeferMissing();
        $this->assertEquals('foo', $m->bar());
        $m->shouldReceive('bar')->andReturn(123);
        $this->assertEquals(123, $m->bar());
    }

    /**
     * @expectedException BadMethodCallException
     */
    public function test_named_mock_with_should_defer_missing_throws_if_not_available()
    {
        $m = $this->container->mock('MockeryTest_ClassConstructor2', [$param1 = new stdClass]);
        $m->shouldDeferMissing();
        $m->foorbar123();
    }

    public function test_mocking_a_known_concrete_class_so_mock_inherits_class_type()
    {
        $m = $this->container->mock('stdClass');
        $m->shouldReceive('foo')->andReturn('bar');
        $this->assertEquals('bar', $m->foo());
        $this->assertTrue($m instanceof stdClass);
    }

    public function test_mocking_a_known_user_class_so_mock_inherits_class_type()
    {
        $m = $this->container->mock('MockeryTest_TestInheritedType');
        $this->assertTrue($m instanceof MockeryTest_TestInheritedType);
    }

    public function test_mocking_a_concrete_object_creates_a_partial_without_error()
    {
        $m = $this->container->mock(new stdClass);
        $m->shouldReceive('foo')->andReturn('bar');
        $this->assertEquals('bar', $m->foo());
        $this->assertTrue($m instanceof stdClass);
    }

    public function test_creating_a_partial_allows_dynamic_expectations_and_passes_through_unexpected_methods()
    {
        $m = $this->container->mock(new MockeryTestFoo);
        $m->shouldReceive('bar')->andReturn('bar');
        $this->assertEquals('bar', $m->bar());
        $this->assertEquals('foo', $m->foo());
        $this->assertTrue($m instanceof MockeryTestFoo);
    }

    public function test_creating_a_partial_allows_expectations_to_intercept_calls_to_implemented_methods()
    {
        $m = $this->container->mock(new MockeryTestFoo2);
        $m->shouldReceive('bar')->andReturn('baz');
        $this->assertEquals('baz', $m->bar());
        $this->assertEquals('foo', $m->foo());
        $this->assertTrue($m instanceof MockeryTestFoo2);
    }

    public function test_block_forwarding_to_partial_object()
    {
        $m = $this->container->mock(new MockeryTestBar1, ['foo' => 1, Mockery\Container::BLOCKS => ['method1']]);
        $this->assertSame($m, $m->method1());
    }

    public function test_partial_with_array_defs()
    {
        $m = $this->container->mock(new MockeryTestBar1, ['foo' => 1, Mockery\Container::BLOCKS => ['method1']]);
        $this->assertEquals(1, $m->foo());
    }

    public function test_passing_closure_as_final_parameter_used_to_define_expectations()
    {
        $m = $this->container->mock('foo', function ($m) {
            $m->shouldReceive('foo')->once()->andReturn('bar');
        });
        $this->assertEquals('bar', $m->foo());
    }

    /**
     * @expectedException \Mockery\Exception
     */
    public function test_mocking_a_known_concrete_final_class_throws_errors_only_partial_mocks_can_mock_final_elements()
    {
        $m = $this->container->mock('MockeryFoo3');
    }

    public function test_mocking_a_known_concrete_class_with_final_methods_throws_no_exception()
    {
        $m = $this->container->mock('MockeryFoo4');
    }

    /**
     * @group finalclass
     */
    public function test_final_classes_can_be_partial_mocks()
    {
        $m = $this->container->mock(new MockeryFoo3);
        $m->shouldReceive('foo')->andReturn('baz');
        $this->assertEquals('baz', $m->foo());
        $this->assertFalse($m instanceof MockeryFoo3);
    }

    public function test_spl_class_with_final_methods_can_be_mocked()
    {
        $m = $this->container->mock('SplFileInfo');
        $m->shouldReceive('foo')->andReturn('baz');
        $this->assertEquals('baz', $m->foo());
        $this->assertTrue($m instanceof SplFileInfo);
    }

    public function test_spl_class_with_final_methods_can_be_mocked_multiple_times()
    {
        $this->container->mock('SplFileInfo');
        $m = $this->container->mock('SplFileInfo');
        $m->shouldReceive('foo')->andReturn('baz');
        $this->assertEquals('baz', $m->foo());
        $this->assertTrue($m instanceof SplFileInfo);
    }

    public function test_classes_with_final_methods_can_be_proxy_partial_mocks()
    {
        $m = $this->container->mock(new MockeryFoo4);
        $m->shouldReceive('foo')->andReturn('baz');
        $this->assertEquals('baz', $m->foo());
        $this->assertEquals('bar', $m->bar());
        $this->assertTrue($m instanceof MockeryFoo4);
    }

    public function test_classes_with_final_methods_can_be_proper_partial_mocks()
    {
        $m = $this->container->mock('MockeryFoo4[bar]');
        $m->shouldReceive('bar')->andReturn('baz');
        $this->assertEquals('baz', $m->foo());
        $this->assertEquals('baz', $m->bar());
        $this->assertTrue($m instanceof MockeryFoo4);
    }

    public function test_classes_with_final_methods_can_be_proper_partial_mocks_but_final_methods_not_partialed()
    {
        $m = $this->container->mock('MockeryFoo4[foo]');
        $m->shouldReceive('foo')->andReturn('foo');
        $this->assertEquals('baz', $m->foo()); // partial expectation ignored - will fail callcount assertion
        $this->assertTrue($m instanceof MockeryFoo4);
    }

    public function test_splfileinfo_class_mock_passes_user_expectations()
    {
        $file = $this->container->mock('SplFileInfo[getFilename,getPathname,getExtension,getMTime]', [__FILE__]);
        $file->shouldReceive('getFilename')->once()->andReturn('foo');
        $file->shouldReceive('getPathname')->once()->andReturn('path/to/foo');
        $file->shouldReceive('getExtension')->once()->andReturn('css');
        $file->shouldReceive('getMTime')->once()->andReturn(time());
    }

    public function test_can_mock_interface()
    {
        $m = $this->container->mock('MockeryTest_Interface');
        $this->assertTrue($m instanceof MockeryTest_Interface);
    }

    public function test_can_mock_spl()
    {
        $m = $this->container->mock('\\SplFixedArray');
        $this->assertTrue($m instanceof SplFixedArray);
    }

    public function test_can_mock_interface_with_abstract_method()
    {
        $m = $this->container->mock('MockeryTest_InterfaceWithAbstractMethod');
        $this->assertTrue($m instanceof MockeryTest_InterfaceWithAbstractMethod);
        $m->shouldReceive('foo')->andReturn(1);
        $this->assertEquals(1, $m->foo());
    }

    public function test_can_mock_abstract_with_abstract_protected_method()
    {
        $m = $this->container->mock('MockeryTest_AbstractWithAbstractMethod');
        $this->assertTrue($m instanceof MockeryTest_AbstractWithAbstractMethod);
    }

    public function test_can_mock_interface_with_public_static_method()
    {
        $m = $this->container->mock('MockeryTest_InterfaceWithPublicStaticMethod');
        $this->assertTrue($m instanceof MockeryTest_InterfaceWithPublicStaticMethod);
    }

    public function test_can_mock_class_with_constructor()
    {
        $m = $this->container->mock('MockeryTest_ClassConstructor');
        $this->assertTrue($m instanceof MockeryTest_ClassConstructor);
    }

    public function test_can_mock_class_with_constructor_needing_class_args()
    {
        $m = $this->container->mock('MockeryTest_ClassConstructor2');
        $this->assertTrue($m instanceof MockeryTest_ClassConstructor2);
    }

    /**
     * @group partial
     */
    public function test_can_partially_mock_a_normal_class()
    {
        $m = $this->container->mock('MockeryTest_PartialNormalClass[foo]');
        $this->assertTrue($m instanceof MockeryTest_PartialNormalClass);
        $m->shouldReceive('foo')->andReturn('cba');
        $this->assertEquals('abc', $m->bar());
        $this->assertEquals('cba', $m->foo());
    }

    /**
     * @group partial
     */
    public function test_can_partially_mock_an_abstract_class()
    {
        $m = $this->container->mock('MockeryTest_PartialAbstractClass[foo]');
        $this->assertTrue($m instanceof MockeryTest_PartialAbstractClass);
        $m->shouldReceive('foo')->andReturn('cba');
        $this->assertEquals('abc', $m->bar());
        $this->assertEquals('cba', $m->foo());
    }

    /**
     * @group partial
     */
    public function test_can_partially_mock_a_normal_class_with2_methods()
    {
        $m = $this->container->mock('MockeryTest_PartialNormalClass2[foo, baz]');
        $this->assertTrue($m instanceof MockeryTest_PartialNormalClass2);
        $m->shouldReceive('foo')->andReturn('cba');
        $m->shouldReceive('baz')->andReturn('cba');
        $this->assertEquals('abc', $m->bar());
        $this->assertEquals('cba', $m->foo());
        $this->assertEquals('cba', $m->baz());
    }

    /**
     * @group partial
     */
    public function test_can_partially_mock_an_abstract_class_with2_methods()
    {
        $m = $this->container->mock('MockeryTest_PartialAbstractClass2[foo,baz]');
        $this->assertTrue($m instanceof MockeryTest_PartialAbstractClass2);
        $m->shouldReceive('foo')->andReturn('cba');
        $m->shouldReceive('baz')->andReturn('cba');
        $this->assertEquals('abc', $m->bar());
        $this->assertEquals('cba', $m->foo());
        $this->assertEquals('cba', $m->baz());
    }

    /**
     * @expectedException \Mockery\Exception
     *
     * @group partial
     */
    public function test_throws_exception_if_setting_expectation_for_non_mocked_method_of_partial_mock()
    {
        $this->markTestSkipped('For now...');
        $m = $this->container->mock('MockeryTest_PartialNormalClass[foo]');
        $this->assertTrue($m instanceof MockeryTest_PartialNormalClass);
        $m->shouldReceive('bar')->andReturn('cba');
    }

    /**
     * @expectedException \Mockery\Exception
     *
     * @group partial
     */
    public function test_throws_exception_if_class_or_interface_for_partial_mock_does_not_exist()
    {
        $m = $this->container->mock('MockeryTest_PartialNormalClassXYZ[foo]');
    }

    /**
     * @group issue/4
     */
    public function test_can_mock_class_containing_magic_call_method()
    {
        $m = $this->container->mock('MockeryTest_Call1');
        $this->assertTrue($m instanceof MockeryTest_Call1);
    }

    /**
     * @group issue/4
     */
    public function test_can_mock_class_containing_magic_call_method_without_type_hinting()
    {
        $m = $this->container->mock('MockeryTest_Call2');
        $this->assertTrue($m instanceof MockeryTest_Call2);
    }

    /**
     * @group issue/14
     */
    public function test_can_mock_class_containing_a_public_wakeup_method()
    {
        $m = $this->container->mock('MockeryTest_Wakeup1');
        $this->assertTrue($m instanceof MockeryTest_Wakeup1);
    }

    /**
     * @group issue/18
     */
    public function test_can_mock_class_using_magic_call_methods_in_place_of_normal_methods()
    {
        $m = Mockery::mock('Gateway');
        $m->shouldReceive('iDoSomethingReallyCoolHere');
        $m->iDoSomethingReallyCoolHere();
    }

    /**
     * @group issue/18
     */
    public function test_can_partial_mock_object_using_magic_call_methods_in_place_of_normal_methods()
    {
        $m = Mockery::mock(new Gateway);
        $m->shouldReceive('iDoSomethingReallyCoolHere');
        $m->iDoSomethingReallyCoolHere();
    }

    /**
     * @group issue/13
     */
    public function test_can_mock_class_where_method_has_referenced_parameter()
    {
        $m = Mockery::mock(new MockeryTest_MethodParamRef);
    }

    /**
     * @group issue/13
     */
    public function test_can_partially_mock_object_where_method_has_referenced_parameter()
    {
        $m = Mockery::mock(new MockeryTest_MethodParamRef2);
    }

    /**
     * @group issue/11
     */
    public function test_mocking_a_known_concrete_class_can_be_granted_an_arbitrary_class_type()
    {
        $m = $this->container->mock('alias:MyNamespace\MyClass');
        $m->shouldReceive('foo')->andReturn('bar');
        $this->assertEquals('bar', $m->foo());
        $this->assertTrue($m instanceof MyNamespace\MyClass);
    }

    /**
     * @group issue/15
     */
    public function test_can_mock_multiple_interfaces()
    {
        $m = $this->container->mock('MockeryTest_Interface1, MockeryTest_Interface2');
        $this->assertTrue($m instanceof MockeryTest_Interface1);
        $this->assertTrue($m instanceof MockeryTest_Interface2);
    }

    /**
     * @expectedException \Mockery\Exception
     */
    public function test_mocking_multiple_interfaces_throws_exception_when_given_non_existing_class_or_interface()
    {
        $m = $this->container->mock('DoesNotExist, MockeryTest_Interface2');
        $this->assertTrue($m instanceof MockeryTest_Interface1);
        $this->assertTrue($m instanceof MockeryTest_Interface2);
    }

    /**
     * @group issue/15
     */
    public function test_can_mock_class_and_apply_multiple_interfaces()
    {
        $m = $this->container->mock('MockeryTestFoo, MockeryTest_Interface1, MockeryTest_Interface2');
        $this->assertTrue($m instanceof MockeryTestFoo);
        $this->assertTrue($m instanceof MockeryTest_Interface1);
        $this->assertTrue($m instanceof MockeryTest_Interface2);
    }

    /**
     * @group issue/7
     *
     * Noted: We could complicate internally, but a blind class is better built
     * with a real class noted up front (stdClass is a perfect choice it is
     * behaviourless). Fine, it's a muddle - but we need to draw a line somewhere.
     */
    public function test_can_mock_static_methods()
    {
        Mockery::setContainer($this->container);
        $m = $this->container->mock('alias:MyNamespace\MyClass2');
        $m->shouldReceive('staticFoo')->andReturn('bar');
        $this->assertEquals('bar', \MyNameSpace\MyClass2::staticFoo());
        Mockery::resetContainer();
    }

    /**
     * @group issue/7
     *
     * @expectedException \Mockery\CountValidator\Exception
     */
    public function test_mocked_static_methods_obey_method_counting()
    {
        Mockery::setContainer($this->container);
        $m = $this->container->mock('alias:MyNamespace\MyClass3');
        $m->shouldReceive('staticFoo')->once()->andReturn('bar');
        $this->container->mockery_verify();
        Mockery::resetContainer();
    }

    /**
     * @expectedException BadMethodCallException
     */
    public function test_mocked_static_throws_exception_when_method_does_not_exist()
    {
        Mockery::setContainer($this->container);
        $m = $this->container->mock('alias:MyNamespace\StaticNoMethod');
        $this->assertEquals('bar', MyNameSpace\StaticNoMethod::staticFoo());
        Mockery::resetContainer();
    }

    /**
     * @group issue/17
     */
    public function test_mocking_allows_public_property_stubbing_on_real_class()
    {
        $m = $this->container->mock('MockeryTestFoo');
        $m->foo = 'bar';
        $this->assertEquals('bar', $m->foo);
        // $this->assertTrue(array_key_exists('foo', $m->mockery_getMockableProperties()));
    }

    /**
     * @group issue/17
     */
    public function test_mocking_allows_public_property_stubbing_on_named_mock()
    {
        $m = $this->container->mock('Foo');
        $m->foo = 'bar';
        $this->assertEquals('bar', $m->foo);
        // $this->assertTrue(array_key_exists('foo', $m->mockery_getMockableProperties()));
    }

    /**
     * @group issue/17
     */
    public function test_mocking_allows_public_property_stubbing_on_partials()
    {
        $m = $this->container->mock(new stdClass);
        $m->foo = 'bar';
        $this->assertEquals('bar', $m->foo);
        // $this->assertTrue(array_key_exists('foo', $m->mockery_getMockableProperties()));
    }

    /**
     * @group issue/17
     */
    public function test_mocking_does_not_stub_non_stubbed_properties_on_partials()
    {
        $m = $this->container->mock(new MockeryTest_ExistingProperty);
        $this->assertEquals('bar', $m->foo);
        $this->assertFalse(array_key_exists('foo', $m->mockery_getMockableProperties()));
    }

    public function test_creation_of_instance_mock()
    {
        $m = $this->container->mock('overload:MyNamespace\MyClass4');
        $this->assertTrue($m instanceof MyNamespace\MyClass4);
    }

    public function test_instantiation_of_instance_mock()
    {
        Mockery::setContainer($this->container);
        $m = $this->container->mock('overload:MyNamespace\MyClass5');
        $instance = new MyNamespace\MyClass5;
        $this->assertTrue($instance instanceof MyNamespace\MyClass5);
        Mockery::resetContainer();
    }

    public function test_instantiation_of_instance_mock_imports_expectations()
    {
        Mockery::setContainer($this->container);
        $m = $this->container->mock('overload:MyNamespace\MyClass6');
        $m->shouldReceive('foo')->andReturn('bar');
        $instance = new MyNamespace\MyClass6;
        $this->assertEquals('bar', $instance->foo());
        Mockery::resetContainer();
    }

    public function test_instantiation_of_instance_mocks_ignores_verification_of_origin_mock()
    {
        Mockery::setContainer($this->container);
        $m = $this->container->mock('overload:MyNamespace\MyClass7');
        $m->shouldReceive('foo')->once()->andReturn('bar');
        $this->container->mockery_verify();
        Mockery::resetContainer(); // should not throw an exception
    }

    /**
     * @expectedException \Mockery\CountValidator\Exception
     */
    public function test_instantiation_of_instance_mocks_adds_them_to_container_for_verification()
    {
        Mockery::setContainer($this->container);
        $m = $this->container->mock('overload:MyNamespace\MyClass8');
        $m->shouldReceive('foo')->once();
        $instance = new MyNamespace\MyClass8;
        $this->container->mockery_verify();
        Mockery::resetContainer();
    }

    public function test_instantiation_of_instance_mocks_does_not_have_count_validator_crossover()
    {
        Mockery::setContainer($this->container);
        $m = $this->container->mock('overload:MyNamespace\MyClass9');
        $m->shouldReceive('foo')->once();
        $instance1 = new MyNamespace\MyClass9;
        $instance2 = new MyNamespace\MyClass9;
        $instance1->foo();
        $instance2->foo();
        $this->container->mockery_verify();
        Mockery::resetContainer();
    }

    /**
     * @expectedException \Mockery\CountValidator\Exception
     */
    public function test_instantiation_of_instance_mocks_does_not_have_count_validator_crossover2()
    {
        Mockery::setContainer($this->container);
        $m = $this->container->mock('overload:MyNamespace\MyClass10');
        $m->shouldReceive('foo')->once();
        $instance1 = new MyNamespace\MyClass10;
        $instance2 = new MyNamespace\MyClass10;
        $instance1->foo();
        $this->container->mockery_verify();
        Mockery::resetContainer();
    }

    public function test_creation_of_instance_mock_with_fully_qualified_name()
    {
        $m = $this->container->mock('overload:\MyNamespace\MyClass11');
        $this->assertTrue($m instanceof MyNamespace\MyClass11);
    }

    public function test_instance_mocks_should_ignore_missing()
    {
        Mockery::setContainer($this->container);
        $m = $this->container->mock('overload:MyNamespace\MyClass12');
        $m->shouldIgnoreMissing();

        $instance = new MyNamespace\MyClass12;
        $instance->foo();

        Mockery::resetContainer();
    }

    public function test_method_params_passed_by_reference_have_reference_preserved()
    {
        $m = $this->container->mock('MockeryTestRef1');
        $m->shouldReceive('foo')->with(
            Mockery::on(function (&$a) {
                $a += 1;

                return true;
            }),
            Mockery::any()
        );
        $a = 1;
        $b = 1;
        $m->foo($a, $b);
        $this->assertEquals(2, $a);
        $this->assertEquals(1, $b);
    }

    /**
     * Meant to test the same logic as
     * testCanOverrideExpectedParametersOfExtensionPHPClassesToPreserveRefs,
     * but:
     * - doesn't require an extension
     * - isn't actually known to be used
     */
    public function test_can_override_expected_parameters_of_internal_php_classes_to_preserve_refs()
    {
        Mockery::getConfiguration()->setInternalClassMethodParamMap(
            'DateTime', 'modify', ['&$string']
        );
        // @ used to avoid E_STRICT for incompatible signature
        @$m = $this->container->mock('DateTime');
        $this->assertInstanceOf("Mockery\MockInterface", $m, 'Mocking failed, remove @ error suppresion to debug');
        $m->shouldReceive('modify')->with(
            Mockery::on(function (&$string) {
                $string = 'foo';

                return true;
            })
        );
        $data = 'bar';
        $m->modify($data);
        $this->assertEquals('foo', $data);
        $this->container->mockery_verify();
        Mockery::resetContainer();
        Mockery::getConfiguration()->resetInternalClassMethodParamMaps();
    }

    /**
     * Real world version of
     * testCanOverrideExpectedParametersOfInternalPHPClassesToPreserveRefs
     */
    public function test_can_override_expected_parameters_of_extension_php_classes_to_preserve_refs()
    {
        if (! class_exists('MongoCollection', false)) {
            $this->markTestSkipped('ext/mongo not installed');
        }
        Mockery::getConfiguration()->setInternalClassMethodParamMap(
            'MongoCollection', 'insert', ['&$data', '$options']
        );
        // @ used to avoid E_STRICT for incompatible signature
        @$m = $this->container->mock('MongoCollection');
        $this->assertInstanceOf("Mockery\MockInterface", $m, 'Mocking failed, remove @ error suppresion to debug');
        $m->shouldReceive('insert')->with(
            Mockery::on(function (&$data) {
                $data['_id'] = 123;

                return true;
            }),
            Mockery::type('array')
        );
        $data = ['a' => 1, 'b' => 2];
        $m->insert($data, []);
        $this->assertTrue(isset($data['_id']));
        $this->assertEquals(123, $data['_id']);
        $this->container->mockery_verify();
        Mockery::resetContainer();
        Mockery::getConfiguration()->resetInternalClassMethodParamMaps();
    }

    public function test_can_create_non_overriden_instance_of_previously_overriden_internal_classes()
    {
        Mockery::getConfiguration()->setInternalClassMethodParamMap(
            'DateTime', 'modify', ['&$string']
        );
        // @ used to avoid E_STRICT for incompatible signature
        @$m = $this->container->mock('DateTime');
        $this->assertInstanceOf("Mockery\MockInterface", $m, 'Mocking failed, remove @ error suppresion to debug');
        $rc = new ReflectionClass($m);
        $rm = $rc->getMethod('modify');
        $params = $rm->getParameters();
        $this->assertTrue($params[0]->isPassedByReference());

        Mockery::getConfiguration()->resetInternalClassMethodParamMaps();

        $m = $this->container->mock('DateTime');
        $this->assertInstanceOf("Mockery\MockInterface", $m, 'Mocking failed');
        $rc = new ReflectionClass($m);
        $rm = $rc->getMethod('modify');
        $params = $rm->getParameters();
        $this->assertFalse($params[0]->isPassedByReference());

        Mockery::resetContainer();
        Mockery::getConfiguration()->resetInternalClassMethodParamMaps();
    }

    /**
     * @group abstract
     */
    public function test_can_mock_abstract_class_with_abstract_public_method()
    {
        $m = $this->container->mock('MockeryTest_AbstractWithAbstractPublicMethod');
        $this->assertTrue($m instanceof MockeryTest_AbstractWithAbstractPublicMethod);
    }

    /**
     * @issue issue/21
     */
    public function test_class_declaring_isset_does_not_throw_exception()
    {
        Mockery::setContainer($this->container);
        $m = $this->container->mock('MockeryTest_IssetMethod');
        $this->container->mockery_verify();
        Mockery::resetContainer();
    }

    /**
     * @issue issue/21
     */
    public function test_class_declaring_unset_does_not_throw_exception()
    {
        Mockery::setContainer($this->container);
        $m = $this->container->mock('MockeryTest_UnsetMethod');
        $this->container->mockery_verify();
        Mockery::resetContainer();
    }

    /**
     * @issue issue/35
     */
    public function test_calling_self_only_returns_last_mock_created_or_current_mock_being_programmed_since_they_are_one_and_the_same()
    {
        Mockery::setContainer($this->container);
        $m = $this->container->mock('MockeryTestFoo');
        $this->assertFalse($this->container->self() instanceof MockeryTestFoo2);
        // $m = $this->container->mock('MockeryTestFoo2');
        // $this->assertTrue($this->container->self() instanceof MockeryTestFoo2);
        // $m = $this->container->mock('MockeryTestFoo');
        // $this->assertFalse(Mockery::self() instanceof MockeryTestFoo2);
        // $this->assertTrue(Mockery::self() instanceof MockeryTestFoo);
        Mockery::resetContainer();
    }

    /**
     * @issue issue/89
     */
    public function test_creating_mock_of_class_with_existing_to_string_method_doesnt_create_class_with_two_to_string_methods()
    {
        Mockery::setContainer($this->container);
        $m = $this->container->mock('MockeryTest_WithToString'); // this would fatal
        $m->shouldReceive('__toString')->andReturn('dave');
        $this->assertEquals('dave', "$m");
    }

    public function test_get_expectation_count_fresh_container()
    {
        $this->assertEquals(0, $this->container->mockery_getExpectationCount());
    }

    public function test_get_expectation_count_simplest_mock()
    {
        $m = $this->container->mock();
        $m->shouldReceive('foo')->andReturn('bar');
        $this->assertEquals(1, $this->container->mockery_getExpectationCount());
    }

    public function test_methods_returning_params_by_reference_does_not_error_out()
    {
        $this->container->mock('MockeryTest_ReturnByRef');
        $mock = $this->container->mock('MockeryTest_ReturnByRef');
        $mock->shouldReceive('get')->andReturn($var = 123);
        $this->assertSame($var, $mock->get());
    }

    public function test_mock_callable_type_hint()
    {
        if (PHP_VERSION_ID >= 50400) {
            $this->container->mock('MockeryTest_MockCallableTypeHint');
        }
    }

    public function test_can_mock_class_with_reserved_word_method()
    {
        if (! extension_loaded('redis')) {
            $this->markTestSkipped('phpredis not installed');
        }

        $this->container->mock('Redis');
    }

    public function test_undeclared_class_is_declared()
    {
        $this->assertFalse(class_exists('BlahBlah'));
        $mock = $this->container->mock('BlahBlah');
        $this->assertInstanceOf('BlahBlah', $mock);
    }

    public function test_undeclared_class_with_namespace_is_declared()
    {
        $this->assertFalse(class_exists("MyClasses\Blah\BlahBlah"));
        $mock = $this->container->mock("MyClasses\Blah\BlahBlah");
        $this->assertInstanceOf("MyClasses\Blah\BlahBlah", $mock);
    }

    public function test_undeclared_class_with_namespace_including_leading_operator_is_declared()
    {
        $this->assertFalse(class_exists("\MyClasses\DaveBlah\BlahBlah"));
        $mock = $this->container->mock("\MyClasses\DaveBlah\BlahBlah");
        $this->assertInstanceOf("\MyClasses\DaveBlah\BlahBlah", $mock);
    }

    public function test_mocking_phpredis_extension_class_works()
    {
        if (! class_exists('Redis')) {
            $this->markTestSkipped('PHPRedis extension required for this test');
        }
        $m = $this->container->mock('Redis');
    }

    public function test_isset_mapping_using_proxied_partials_check_no_exception_thrown()
    {
        $var = $this->container->mock(new MockeryTestIsset_Bar);
        $mock = $this->container->mock(new MockeryTestIsset_Foo($var));
        $mock->shouldReceive('bar')->once();
        $mock->bar();
        $this->container->mockery_teardown(); // closed by teardown()
    }

    /**
     * @group traversable1
     */
    public function test_can_mock_interfaces_extending_traversable()
    {
        $mock = $this->container->mock('MockeryTest_InterfaceWithTraversable');
        $this->assertInstanceOf('MockeryTest_InterfaceWithTraversable', $mock);
        $this->assertInstanceOf('ArrayAccess', $mock);
        $this->assertInstanceOf('Countable', $mock);
        $this->assertInstanceOf('Traversable', $mock);
    }

    /**
     * @group traversable2
     */
    public function test_can_mock_interfaces_alongside_traversable()
    {
        $mock = $this->container->mock('stdClass, ArrayAccess, Countable, Traversable');
        $this->assertInstanceOf('stdClass', $mock);
        $this->assertInstanceOf('ArrayAccess', $mock);
        $this->assertInstanceOf('Countable', $mock);
        $this->assertInstanceOf('Traversable', $mock);
    }

    public function test_interfaces_can_have_assertions()
    {
        Mockery::setContainer($this->container);
        $m = $this->container->mock('stdClass, ArrayAccess, Countable, Traversable');
        $m->shouldReceive('foo')->once();
        $m->foo();
        $this->container->mockery_verify();
        Mockery::resetContainer();
    }

    public function test_mocking_iterator_aggregate_does_not_implement_iterator()
    {
        $mock = $this->container->mock('MockeryTest_ImplementsIteratorAggregate');
        $this->assertInstanceOf('IteratorAggregate', $mock);
        $this->assertInstanceOf('Traversable', $mock);
        $this->assertNotInstanceOf('Iterator', $mock);
    }

    public function test_mocking_interface_that_extends_iterator_does_not_implement_iterator()
    {
        $mock = $this->container->mock('MockeryTest_InterfaceThatExtendsIterator');
        $this->assertInstanceOf('Iterator', $mock);
        $this->assertInstanceOf('Traversable', $mock);
    }

    public function test_mocking_interface_that_extends_iterator_aggregate_does_not_implement_iterator()
    {
        $mock = $this->container->mock('MockeryTest_InterfaceThatExtendsIteratorAggregate');
        $this->assertInstanceOf('IteratorAggregate', $mock);
        $this->assertInstanceOf('Traversable', $mock);
        $this->assertNotInstanceOf('Iterator', $mock);
    }

    public function test_mocking_iterator_aggregate_does_not_implement_iterator_alongside()
    {
        $mock = $this->container->mock('IteratorAggregate');
        $this->assertInstanceOf('IteratorAggregate', $mock);
        $this->assertInstanceOf('Traversable', $mock);
        $this->assertNotInstanceOf('Iterator', $mock);
    }

    public function test_mocking_iterator_does_not_implement_iterator_alongside()
    {
        $mock = $this->container->mock('Iterator');
        $this->assertInstanceOf('Iterator', $mock);
        $this->assertInstanceOf('Traversable', $mock);
    }

    public function test_mocking_iterator_does_not_implement_iterator()
    {
        $mock = $this->container->mock('MockeryTest_ImplementsIterator');
        $this->assertInstanceOf('Iterator', $mock);
        $this->assertInstanceOf('Traversable', $mock);
    }

    public function test_mockery_close_for_illegal_isset_file_include()
    {
        $m = Mockery::mock('StdClass')
            ->shouldReceive('get')
            ->andReturn(false)
            ->getMock();
        $m->get();
        Mockery::close();
    }

    public function test_mockery_should_distinguish_between_constructor_params_and_closures()
    {
        $obj = new MockeryTestFoo;
        $mock = $this->container->mock('MockeryTest_ClassMultipleConstructorParams[dave]',
            [&$obj, 'foo']);
    }

    /** @group nette */
    public function test_mockery_should_not_mock_callstatic_magic_method()
    {
        $mock = $this->container->mock('MockeryTest_CallStatic');
    }

    /**
     * @issue issue/139
     */
    public function test_can_mock_class_with_old_style_constructor_and_arguments()
    {
        $mock = $this->container->mock('MockeryTest_OldStyleConstructor');
    }

    /** @group issue/144 */
    public function test_mockery_should_interpret_empty_array_as_constructor_args()
    {
        $mock = $this->container->mock('EmptyConstructorTest', []);
        $this->assertSame(0, $mock->numberOfConstructorArgs);
    }

    /** @group issue/144 */
    public function test_mockery_should_call_constructor_by_default_when_requesting_partials()
    {
        $mock = $this->container->mock('EmptyConstructorTest[foo]');
        $this->assertSame(0, $mock->numberOfConstructorArgs);
    }

    /** @group issue/158 */
    public function test_mockery_should_respect_interface_with_method_param_self()
    {
        $this->container->mock('MockeryTest_InterfaceWithMethodParamSelf');
    }

    /** @group issue/162 */
    public function test_mockery_doesnt_try_and_mock_lowercase_to_string()
    {
        $this->container->mock('MockeryTest_Lowercase_ToString');
    }

    /** @group issue/175 */
    public function test_existing_static_method_mocking()
    {
        Mockery::setContainer($this->container);
        $mock = $this->container->mock('MockeryTest_PartialStatic[mockMe]');

        $mock->shouldReceive('mockMe')->with(5)->andReturn(10);

        $this->assertEquals(10, $mock::mockMe(5));
        $this->assertEquals(3, $mock::keepMe(3));
    }

    /**
     * @group issue/154
     *
     * @expectedException InvalidArgumentException
     *
     * @expectedExceptionMessage protectedMethod() cannot be mocked as it a protected method and mocking protected methods is not allowed for this mock
     */
    public function test_should_throw_if_attempting_to_stub_protected_method()
    {
        $mock = $this->container->mock('MockeryTest_WithProtectedAndPrivate');
        $mock->shouldReceive('protectedMethod');
    }

    /**
     * @group issue/154
     *
     * @expectedException InvalidArgumentException
     *
     * @expectedExceptionMessage privateMethod() cannot be mocked as it is a private method
     */
    public function test_should_throw_if_attempting_to_stub_private_method()
    {
        $mock = $this->container->mock('MockeryTest_WithProtectedAndPrivate');
        $mock->shouldReceive('privateMethod');
    }

    public function test_wakeup_magic_is_not_mocked_to_allow_serialisation_instance_hack()
    {
        $mock = $this->container->mock('DateTime');
    }

    /**
     * @group issue/154
     */
    public function test_can_mock_methods_with_required_params_that_have_default_values()
    {
        $mock = $this->container->mock('MockeryTest_MethodWithRequiredParamWithDefaultValue');
        $mock->shouldIgnoreMissing();
        $mock->foo(null, 123);
    }

    /**
     * @test
     *
     * @group issue/294
     *
     * @expectedException Mockery\Exception\RuntimeException
     *
     * @expectedExceptionMessage Could not load mock DateTime, class already exists
     */
    public function test_throws_when_named_mock_class_exists_and_is_not_mockery()
    {
        $builder = new MockConfigurationBuilder;
        $builder->setName('DateTime');
        $mock = $this->container->mock($builder);
    }

    /**
     * @expectedException Mockery\Exception\NoMatchingExpectationException
     *
     * @expectedExceptionMessage MyTestClass::foo(resource(...))
     */
    public function test_handles_method_with_argument_expectation_when_called_with_resource()
    {
        $mock = $this->container->mock('MyTestClass');
        $mock->shouldReceive('foo')->with(['yourself' => 21]);

        $mock->foo(fopen('php://memory', 'r'));
    }

    /**
     * @expectedException Mockery\Exception\NoMatchingExpectationException
     *
     * @expectedExceptionMessage MyTestClass::foo(array('myself'=>'array(...)',))
     */
    public function test_handles_method_with_argument_expectation_when_called_with_circular_array()
    {
        $testArray = [];
        $testArray['myself'] = &$testArray;

        $mock = $this->container->mock('MyTestClass');
        $mock->shouldReceive('foo')->with(['yourself' => 21]);

        $mock->foo($testArray);
    }

    /**
     * @expectedException Mockery\Exception\NoMatchingExpectationException
     *
     * @expectedExceptionMessage MyTestClass::foo(array('a_scalar'=>2,'an_array'=>'array(...)',))
     */
    public function test_handles_method_with_argument_expectation_when_called_with_nested_array()
    {
        $testArray = [];
        $testArray['a_scalar'] = 2;
        $testArray['an_array'] = [1, 2, 3];

        $mock = $this->container->mock('MyTestClass');
        $mock->shouldReceive('foo')->with(['yourself' => 21]);

        $mock->foo($testArray);
    }

    /**
     * @expectedException Mockery\Exception\NoMatchingExpectationException
     *
     * @expectedExceptionMessage MyTestClass::foo(array('a_scalar'=>2,'an_object'=>'object(stdClass)',))
     */
    public function test_handles_method_with_argument_expectation_when_called_with_nested_object()
    {
        $testArray = [];
        $testArray['a_scalar'] = 2;
        $testArray['an_object'] = new stdClass;

        $mock = $this->container->mock('MyTestClass');
        $mock->shouldReceive('foo')->with(['yourself' => 21]);

        $mock->foo($testArray);
    }

    /**
     * @expectedException Mockery\Exception\NoMatchingExpectationException
     *
     * @expectedExceptionMessage MyTestClass::foo(array('a_scalar'=>2,'a_closure'=>'object(Closure
     */
    public function test_handles_method_with_argument_expectation_when_called_with_nested_closure()
    {
        $testArray = [];
        $testArray['a_scalar'] = 2;
        $testArray['a_closure'] = function () {};

        $mock = $this->container->mock('MyTestClass');
        $mock->shouldReceive('foo')->with(['yourself' => 21]);

        $mock->foo($testArray);
    }

    /**
     * @expectedException Mockery\Exception\NoMatchingExpectationException
     *
     * @expectedExceptionMessage MyTestClass::foo(array('a_scalar'=>2,'a_resource'=>'resource(...)',))
     */
    public function test_handles_method_with_argument_expectation_when_called_with_nested_resource()
    {
        $testArray = [];
        $testArray['a_scalar'] = 2;
        $testArray['a_resource'] = fopen('php://memory', 'r');

        $mock = $this->container->mock('MyTestClass');
        $mock->shouldReceive('foo')->with(['yourself' => 21]);

        $mock->foo($testArray);
    }

    /**
     * @test
     *
     * @group issue/339
     */
    public function can_mock_classes_that_descend_from_internal_classes()
    {
        $mock = $this->container->mock('MockeryTest_ClassThatDescendsFromInternalClass');
        $this->assertInstanceOf('DateTime', $mock);
    }

    /**
     * @test
     *
     * @group issue/339
     */
    public function can_mock_classes_that_implement_serializable()
    {
        $mock = $this->container->mock('MockeryTest_ClassThatImplementsSerializable');
        $this->assertInstanceOf('Serializable', $mock);
    }

    /**
     * @test
     *
     * @group issue/346
     */
    public function can_mock_internal_classes_that_implement_serializable()
    {
        $mock = $this->container->mock('ArrayObject');
        $this->assertInstanceOf('Serializable', $mock);
    }
}

class MockeryTest_CallStatic
{
    public static function __callStatic($method, $args) {}
}

class MockeryTest_ClassMultipleConstructorParams
{
    public function __construct($a, $b) {}

    public function dave() {}
}

interface MockeryTest_InterfaceWithTraversable extends ArrayAccess, Countable, Traversable
{
    public function self();
}

class MockeryTestIsset_Bar
{
    public function doSomething() {}
}

class MockeryTestIsset_Foo
{
    private $var;

    public function __construct($var)
    {
        $this->var = $var;
    }

    public function __get($name)
    {
        $this->var->doSomething();
    }

    public function __isset($name)
    {
        return (bool) strlen($this->__get($name));
    }
}

class MockeryTest_IssetMethod
{
    protected $_properties = [];

    public function __construct() {}

    public function __isset($property)
    {
        return isset($this->_properties[$property]);
    }
}

class MockeryTest_UnsetMethod
{
    protected $_properties = [];

    public function __construct() {}

    public function __unset($property)
    {
        unset($this->_properties[$property]);
    }
}

class MockeryTestFoo
{
    public function foo()
    {
        return 'foo';
    }
}

class MockeryTestFoo2
{
    public function foo()
    {
        return 'foo';
    }

    public function bar()
    {
        return 'bar';
    }
}

final class MockeryFoo3
{
    public function foo()
    {
        return 'baz';
    }
}

class MockeryFoo4
{
    final public function foo()
    {
        return 'baz';
    }

    public function bar()
    {
        return 'bar';
    }
}

interface MockeryTest_Interface {}
interface MockeryTest_Interface1 {}
interface MockeryTest_Interface2 {}

interface MockeryTest_InterfaceWithAbstractMethod
{
    public function set();
}

interface MockeryTest_InterfaceWithPublicStaticMethod
{
    public static function self();
}

abstract class MockeryTest_AbstractWithAbstractMethod
{
    abstract protected function set();
}

class MockeryTest_WithProtectedAndPrivate
{
    protected function protectedMethod() {}

    private function privateMethod() {}
}

class MockeryTest_ClassConstructor
{
    public function __construct($param1) {}
}

class MockeryTest_ClassConstructor2
{
    protected $param1;

    public function __construct(stdClass $param1)
    {
        $this->param1 = $param1;
    }

    public function getParam1()
    {
        return $this->param1;
    }

    public function foo()
    {
        return 'foo';
    }

    public function bar()
    {
        return $this->foo();
    }
}

class MockeryTest_Call1
{
    public function __call($method, array $params) {}
}

class MockeryTest_Call2
{
    public function __call($method, $params) {}
}

class MockeryTest_Wakeup1
{
    public function __construct() {}

    public function __wakeup() {}
}

class MockeryTest_ExistingProperty
{
    public $foo = 'bar';
}

abstract class MockeryTest_AbstractWithAbstractPublicMethod
{
    abstract public function foo($a, $b);
}

// issue/18
class SoCool
{
    public function iDoSomethingReallyCoolHere()
    {
        return 3;
    }
}

class Gateway
{
    public function __call($method, $args)
    {
        $m = new SoCool;

        return call_user_func_array([$m, $method], $args);
    }
}

class MockeryTestBar1
{
    public function method1()
    {
        return $this;
    }
}

class MockeryTest_ReturnByRef
{
    public $i = 0;

    public function &get()
    {
        return $this->$i;
    }
}

class MockeryTest_MethodParamRef
{
    public function method1(&$foo)
    {
        return true;
    }
}
class MockeryTest_MethodParamRef2
{
    public function method1(&$foo)
    {
        return true;
    }
}
class MockeryTestRef1
{
    public function foo(&$a, $b) {}
}

class MockeryTest_PartialNormalClass
{
    public function foo()
    {
        return 'abc';
    }

    public function bar()
    {
        return 'abc';
    }
}

abstract class MockeryTest_PartialAbstractClass
{
    abstract public function foo();

    public function bar()
    {
        return 'abc';
    }
}

class MockeryTest_PartialNormalClass2
{
    public function foo()
    {
        return 'abc';
    }

    public function bar()
    {
        return 'abc';
    }

    public function baz()
    {
        return 'abc';
    }
}

abstract class MockeryTest_PartialAbstractClass2
{
    abstract public function foo();

    public function bar()
    {
        return 'abc';
    }

    abstract public function baz();
}

class MockeryTest_TestInheritedType {}

if (PHP_VERSION_ID >= 50400) {
    class MockeryTest_MockCallableTypeHint
    {
        public function foo(callable $baz)
        {
            $baz();
        }

        public function bar(?callable $callback = null)
        {
            $callback();
        }
    }
}

class MockeryTest_WithToString
{
    public function __toString() {}
}

class MockeryTest_ImplementsIteratorAggregate implements IteratorAggregate
{
    public function getIterator()
    {
        return new ArrayIterator([]);
    }
}

class MockeryTest_ImplementsIterator implements Iterator
{
    public function rewind() {}

    public function current() {}

    public function key() {}

    public function next() {}

    public function valid() {}
}

class MockeryTest_OldStyleConstructor
{
    public function MockeryTest_OldStyleConstructor($arg) {}
}

class EmptyConstructorTest
{
    public $numberOfConstructorArgs;

    public function __construct()
    {
        $this->numberOfConstructorArgs = count(func_get_args());
    }

    public function foo() {}
}

interface MockeryTest_InterfaceWithMethodParamSelf
{
    public function foo(self $bar);
}

class MockeryTest_Lowercase_ToString
{
    public function __toString() {}
}

class MockeryTest_PartialStatic
{
    public static function mockMe($a)
    {
        return $a;
    }

    public static function keepMe($b)
    {
        return $b;
    }
}

class MockeryTest_MethodWithRequiredParamWithDefaultValue
{
    public function foo(?DateTime $bar, $baz) {}
}

interface MockeryTest_InterfaceThatExtendsIterator extends Iterator
{
    public function foo();
}

interface MockeryTest_InterfaceThatExtendsIteratorAggregate extends IteratorAggregate
{
    public function foo();
}

class MockeryTest_ClassThatDescendsFromInternalClass extends DateTime {}

class MockeryTest_ClassThatImplementsSerializable implements Serializable
{
    public function serialize() {}

    public function unserialize($serialized) {}
}
