<?php

namespace Mockery\Generator;

class MockConfigurationTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function black_listed_methods_should_not_be_in_list_to_be_mocked()
    {
        $config = new MockConfiguration(["Mockery\Generator\\TestSubject"], ['foo']);

        $methods = $config->getMethodsToMock();
        $this->assertEquals(1, count($methods));
        $this->assertEquals('bar', $methods[0]->getName());
    }

    /**
     * @test
     */
    public function black_lists_are_case_insensitive()
    {
        $config = new MockConfiguration(["Mockery\Generator\\TestSubject"], ['FOO']);

        $methods = $config->getMethodsToMock();
        $this->assertEquals(1, count($methods));
        $this->assertEquals('bar', $methods[0]->getName());
    }

    /**
     * @test
     */
    public function only_white_listed_methods_should_be_in_list_to_be_mocked()
    {
        $config = new MockConfiguration(["Mockery\Generator\\TestSubject"], [], ['foo']);

        $methods = $config->getMethodsToMock();
        $this->assertEquals(1, count($methods));
        $this->assertEquals('foo', $methods[0]->getName());
    }

    /**
     * @test
     */
    public function whitelist_over_rules_black_list()
    {
        $config = new MockConfiguration(["Mockery\Generator\\TestSubject"], ['foo'], ['foo']);

        $methods = $config->getMethodsToMock();
        $this->assertEquals(1, count($methods));
        $this->assertEquals('foo', $methods[0]->getName());
    }

    /**
     * @test
     */
    public function white_lists_are_case_insensitive()
    {
        $config = new MockConfiguration(["Mockery\Generator\\TestSubject"], [], ['FOO']);

        $methods = $config->getMethodsToMock();
        $this->assertEquals(1, count($methods));
        $this->assertEquals('foo', $methods[0]->getName());
    }

    /**
     * @test
     */
    public function final_methods_are_excluded()
    {
        $config = new MockConfiguration(["Mockery\Generator\\ClassWithFinalMethod"]);

        $methods = $config->getMethodsToMock();
        $this->assertEquals(1, count($methods));
        $this->assertEquals('bar', $methods[0]->getName());
    }

    /**
     * @test
     */
    public function should_include_methods_from_all_targets()
    {
        $config = new MockConfiguration(['Mockery\\Generator\\TestInterface', 'Mockery\\Generator\\TestInterface2']);
        $methods = $config->getMethodsToMock();
        $this->assertEquals(2, count($methods));
    }

    /**
     * @test
     *
     * @expectedException Mockery\Exception
     */
    public function should_throw_if_target_class_is_final()
    {
        $config = new MockConfiguration(['Mockery\\Generator\\TestFinal']);
        $config->getTargetClass();
    }

    /**
     * @test
     */
    public function should_target_iterator_aggregate_if_trying_to_mock_traversable()
    {
        $config = new MockConfiguration(['\\Traversable']);

        $interfaces = $config->getTargetInterfaces();
        $this->assertEquals(1, count($interfaces));
        $first = array_shift($interfaces);
        $this->assertEquals('IteratorAggregate', $first->getName());
    }

    /**
     * @test
     */
    public function should_target_iterator_aggregate_if_traversable_in_targets_tree()
    {
        $config = new MockConfiguration(["Mockery\Generator\TestTraversableInterface"]);

        $interfaces = $config->getTargetInterfaces();
        $this->assertEquals(2, count($interfaces));
        $this->assertEquals('IteratorAggregate', $interfaces[0]->getName());
        $this->assertEquals("Mockery\Generator\TestTraversableInterface", $interfaces[1]->getName());
    }

    /**
     * @test
     */
    public function should_bring_iterator_to_head_of_target_list_if_traversable_present()
    {
        $config = new MockConfiguration(["Mockery\Generator\TestTraversableInterface2"]);

        $interfaces = $config->getTargetInterfaces();
        $this->assertEquals(2, count($interfaces));
        $this->assertEquals('Iterator', $interfaces[0]->getName());
        $this->assertEquals("Mockery\Generator\TestTraversableInterface2", $interfaces[1]->getName());
    }

    /**
     * @test
     */
    public function should_bring_iterator_aggregate_to_head_of_target_list_if_traversable_present()
    {
        $config = new MockConfiguration(["Mockery\Generator\TestTraversableInterface3"]);

        $interfaces = $config->getTargetInterfaces();
        $this->assertEquals(2, count($interfaces));
        $this->assertEquals('IteratorAggregate', $interfaces[0]->getName());
        $this->assertEquals("Mockery\Generator\TestTraversableInterface3", $interfaces[1]->getName());
    }
}

interface TestTraversableInterface extends \Traversable {}
interface TestTraversableInterface2 extends \Iterator, \Traversable {}
interface TestTraversableInterface3 extends \IteratorAggregate, \Traversable {}

final class TestFinal {}

interface TestInterface
{
    public function foo();
}

interface TestInterface2
{
    public function bar();
}

class TestSubject
{
    public function foo() {}

    public function bar() {}
}

class ClassWithFinalMethod
{
    final public function foo() {}

    public function bar() {}
}
