<?php

class Framework_MockObject_GeneratorTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var PHPUnit_Framework_MockObject_Generator
     */
    protected $generator;

    protected function setUp()
    {
        $this->generator = new PHPUnit_Framework_MockObject_Generator;
    }

    /**
     * @covers PHPUnit_Framework_MockObject_Generator::getMock
     *
     * @expectedException PHPUnit_Framework_Exception
     */
    public function test_get_mock_fails_when_invalid_function_name_is_passed_in_as_a_function_to_mock()
    {
        $this->generator->getMock('StdClass', [0]);
    }

    /**
     * @covers PHPUnit_Framework_MockObject_Generator::getMock
     */
    public function test_get_mock_can_create_non_existing_functions()
    {
        $mock = $this->generator->getMock('StdClass', ['testFunction']);
        $this->assertTrue(method_exists($mock, 'testFunction'));
    }

    /**
     * @covers PHPUnit_Framework_MockObject_Generator::getMock
     *
     * @expectedException PHPUnit_Framework_MockObject_RuntimeException
     *
     * @expectedExceptionMessage duplicates: "foo, foo"
     */
    public function test_get_mock_generator_fails()
    {
        $mock = $this->generator->getMock('StdClass', ['foo', 'foo']);
    }

    /**
     * @covers PHPUnit_Framework_MockObject_Generator::getMockForAbstractClass
     */
    public function test_get_mock_for_abstract_class_does_not_fail_when_faking_interfaces()
    {
        $mock = $this->generator->getMockForAbstractClass('Countable');
        $this->assertTrue(method_exists($mock, 'count'));
    }

    /**
     * @covers PHPUnit_Framework_MockObject_Generator::getMockForAbstractClass
     */
    public function test_get_mock_for_abstract_class_stubbing_abstract_class()
    {
        $mock = $this->generator->getMockForAbstractClass('AbstractMockTestClass');
        $this->assertTrue(method_exists($mock, 'doSomething'));
    }

    /**
     * @covers PHPUnit_Framework_MockObject_Generator::getMockForAbstractClass
     */
    public function test_get_mock_for_abstract_class_with_non_existent_methods()
    {
        $mock = $this->generator->getMockForAbstractClass(
            'AbstractMockTestClass',
            [],
            '',
            true,
            true,
            true,
            ['nonexistentMethod']
        );

        $this->assertTrue(method_exists($mock, 'nonexistentMethod'));
        $this->assertTrue(method_exists($mock, 'doSomething'));
    }

    /**
     * @covers PHPUnit_Framework_MockObject_Generator::getMockForAbstractClass
     */
    public function test_get_mock_for_abstract_class_should_create_stubs_only_for_abstract_method_when_no_methods_were_informed()
    {
        $mock = $this->generator->getMockForAbstractClass('AbstractMockTestClass');

        $mock->expects($this->any())
            ->method('doSomething')
            ->willReturn('testing');

        $this->assertEquals('testing', $mock->doSomething());
        $this->assertEquals(1, $mock->returnAnything());
    }

    /**
     * @dataProvider getMockForAbstractClassExpectsInvalidArgumentExceptionDataprovider
     *
     * @covers PHPUnit_Framework_MockObject_Generator::getMockForAbstractClass
     *
     * @expectedException PHPUnit_Framework_Exception
     */
    public function test_get_mock_for_abstract_class_expecting_invalid_argument_exception($className, $mockClassName)
    {
        $mock = $this->generator->getMockForAbstractClass($className, [], $mockClassName);
    }

    /**
     * @covers PHPUnit_Framework_MockObject_Generator::getMockForAbstractClass
     *
     * @expectedException PHPUnit_Framework_MockObject_RuntimeException
     */
    public function test_get_mock_for_abstract_class_abstract_class_does_not_exist()
    {
        $mock = $this->generator->getMockForAbstractClass('Tux');
    }

    /**
     * Dataprovider for test "testGetMockForAbstractClassExpectingInvalidArgumentException"
     */
    public static function getMockForAbstractClassExpectsInvalidArgumentExceptionDataprovider()
    {
        return [
            'className not a string' => [[], ''],
            'mockClassName not a string' => ['Countable', new StdClass],
        ];
    }

    /**
     * @covers PHPUnit_Framework_MockObject_Generator::getMockForTrait
     *
     * @requires PHP 5.4.0
     */
    public function test_get_mock_for_trait_with_non_existent_methods_and_non_abstract_methods()
    {
        $mock = $this->generator->getMockForTrait(
            'AbstractTrait',
            [],
            '',
            true,
            true,
            true,
            ['nonexistentMethod']
        );

        $this->assertTrue(method_exists($mock, 'nonexistentMethod'));
        $this->assertTrue(method_exists($mock, 'doSomething'));
        $this->assertTrue($mock->mockableMethod());
        $this->assertTrue($mock->anotherMockableMethod());
    }

    /**
     * @covers   PHPUnit_Framework_MockObject_Generator::getMockForTrait
     *
     * @requires PHP 5.4.0
     */
    public function test_get_mock_for_trait_stubbing_abstract_method()
    {
        $mock = $this->generator->getMockForTrait('AbstractTrait');
        $this->assertTrue(method_exists($mock, 'doSomething'));
    }

    /**
     * @requires PHP 5.4.0
     */
    public function test_get_mock_for_singleton_with_reflection_success()
    {
        // Probably, this should be moved to tests/autoload.php
        require_once __DIR__.'/_fixture/SingletonClass.php';

        $mock = $this->generator->getMock('SingletonClass', ['doSomething'], [], '', false);
        $this->assertInstanceOf('SingletonClass', $mock);
    }

    /**
     * Same as "testGetMockForSingletonWithReflectionSuccess", but we expect
     * warning for PHP < 5.4.0 since PHPUnit will try to execute private __wakeup
     * on unserialize
     */
    public function test_get_mock_for_singleton_with_unserialize_fail()
    {
        if (version_compare(PHP_VERSION, '5.4.0', '>=')) {
            $this->markTestSkipped('Only for PHP < 5.4.0');
        }

        $this->setExpectedException('PHPUnit_Framework_MockObject_RuntimeException');

        // Probably, this should be moved to tests/autoload.php
        require_once __DIR__.'/_fixture/SingletonClass.php';

        $mock = $this->generator->getMock('SingletonClass', ['doSomething'], [], '', false);
    }

    /**
     * ReflectionClass::getMethods for SoapClient on PHP 5.3 produces PHP Fatal Error
     *
     * @runInSeparateProcess
     */
    public function test_get_mock_for_soap_client_reflection_methods_duplication()
    {
        if (version_compare(PHP_VERSION, '5.4.0', '>=')) {
            $this->markTestSkipped('Only for PHP < 5.4.0');
        }

        $mock = $this->generator->getMock('SoapClient', [], [], '', false);
        $this->assertInstanceOf('SoapClient', $mock);
    }
}
