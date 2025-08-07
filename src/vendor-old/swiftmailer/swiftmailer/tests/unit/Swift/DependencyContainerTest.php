<?php

class One
{
    public $arg1;

    public $arg2;

    public function __construct($arg1 = null, $arg2 = null)
    {
        $this->arg1 = $arg1;
        $this->arg2 = $arg2;
    }
}

class Swift_DependencyContainerTest extends \PHPUnit_Framework_TestCase
{
    private $_container;

    protected function setUp()
    {
        $this->_container = new Swift_DependencyContainer;
    }

    public function test_register_and_lookup_value()
    {
        $this->_container->register('foo')->asValue('bar');
        $this->assertEquals('bar', $this->_container->lookup('foo'));
    }

    public function test_has_returns_true_for_registered_value()
    {
        $this->_container->register('foo')->asValue('bar');
        $this->assertTrue($this->_container->has('foo'));
    }

    public function test_has_returns_false_for_unregistered_value()
    {
        $this->assertFalse($this->_container->has('foo'));
    }

    public function test_register_and_lookup_new_instance()
    {
        $this->_container->register('one')->asNewInstanceOf('One');
        $this->assertInstanceOf('One', $this->_container->lookup('one'));
    }

    public function test_has_returns_true_for_registered_instance()
    {
        $this->_container->register('one')->asNewInstanceOf('One');
        $this->assertTrue($this->_container->has('one'));
    }

    public function test_new_instance_is_always_new()
    {
        $this->_container->register('one')->asNewInstanceOf('One');
        $a = $this->_container->lookup('one');
        $b = $this->_container->lookup('one');
        $this->assertEquals($a, $b);
    }

    public function test_register_and_lookup_shared_instance()
    {
        $this->_container->register('one')->asSharedInstanceOf('One');
        $this->assertInstanceOf('One', $this->_container->lookup('one'));
    }

    public function test_has_returns_true_for_shared_instance()
    {
        $this->_container->register('one')->asSharedInstanceOf('One');
        $this->assertTrue($this->_container->has('one'));
    }

    public function test_multiple_shared_instances_are_same_instance()
    {
        $this->_container->register('one')->asSharedInstanceOf('One');
        $a = $this->_container->lookup('one');
        $b = $this->_container->lookup('one');
        $this->assertEquals($a, $b);
    }

    public function test_new_instance_with_dependencies()
    {
        $this->_container->register('foo')->asValue('FOO');
        $this->_container->register('one')->asNewInstanceOf('One')
            ->withDependencies(['foo']);
        $obj = $this->_container->lookup('one');
        $this->assertSame('FOO', $obj->arg1);
    }

    public function test_new_instance_with_multiple_dependencies()
    {
        $this->_container->register('foo')->asValue('FOO');
        $this->_container->register('bar')->asValue(42);
        $this->_container->register('one')->asNewInstanceOf('One')
            ->withDependencies(['foo', 'bar']);
        $obj = $this->_container->lookup('one');
        $this->assertSame('FOO', $obj->arg1);
        $this->assertSame(42, $obj->arg2);
    }

    public function test_new_instance_with_injected_objects()
    {
        $this->_container->register('foo')->asValue('FOO');
        $this->_container->register('one')->asNewInstanceOf('One');
        $this->_container->register('two')->asNewInstanceOf('One')
            ->withDependencies(['one', 'foo']);
        $obj = $this->_container->lookup('two');
        $this->assertEquals($this->_container->lookup('one'), $obj->arg1);
        $this->assertSame('FOO', $obj->arg2);
    }

    public function test_new_instance_with_add_constructor_value()
    {
        $this->_container->register('one')->asNewInstanceOf('One')
            ->addConstructorValue('x')
            ->addConstructorValue(99);
        $obj = $this->_container->lookup('one');
        $this->assertSame('x', $obj->arg1);
        $this->assertSame(99, $obj->arg2);
    }

    public function test_new_instance_with_add_constructor_lookup()
    {
        $this->_container->register('foo')->asValue('FOO');
        $this->_container->register('bar')->asValue(42);
        $this->_container->register('one')->asNewInstanceOf('One')
            ->addConstructorLookup('foo')
            ->addConstructorLookup('bar');

        $obj = $this->_container->lookup('one');
        $this->assertSame('FOO', $obj->arg1);
        $this->assertSame(42, $obj->arg2);
    }

    public function test_resolved_dependencies_can_be_looked_up()
    {
        $this->_container->register('foo')->asValue('FOO');
        $this->_container->register('one')->asNewInstanceOf('One');
        $this->_container->register('two')->asNewInstanceOf('One')
            ->withDependencies(['one', 'foo']);
        $deps = $this->_container->createDependenciesFor('two');
        $this->assertEquals(
            [$this->_container->lookup('one'), 'FOO'], $deps
        );
    }

    public function test_array_of_dependencies_can_be_specified()
    {
        $this->_container->register('foo')->asValue('FOO');
        $this->_container->register('one')->asNewInstanceOf('One');
        $this->_container->register('two')->asNewInstanceOf('One')
            ->withDependencies([['one', 'foo'], 'foo']);

        $obj = $this->_container->lookup('two');
        $this->assertEquals([$this->_container->lookup('one'), 'FOO'], $obj->arg1);
        $this->assertSame('FOO', $obj->arg2);
    }

    public function test_alias_can_be_set()
    {
        $this->_container->register('foo')->asValue('FOO');
        $this->_container->register('bar')->asAliasOf('foo');

        $this->assertSame('FOO', $this->_container->lookup('bar'));
    }

    public function test_alias_of_alias_can_be_set()
    {
        $this->_container->register('foo')->asValue('FOO');
        $this->_container->register('bar')->asAliasOf('foo');
        $this->_container->register('zip')->asAliasOf('bar');
        $this->_container->register('button')->asAliasOf('zip');

        $this->assertSame('FOO', $this->_container->lookup('button'));
    }
}
