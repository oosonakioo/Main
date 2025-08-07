<?php

namespace Hamcrest\Core;

class SetTest extends \Hamcrest\AbstractMatcherTest
{
    public static $_classProperty;

    public $_instanceProperty;

    protected function setUp()
    {
        self::$_classProperty = null;
        unset($this->_instanceProperty);
    }

    protected function createMatcher()
    {
        return \Hamcrest\Core\Set::set('property_name');
    }

    public function test_evaluates_to_true_if_array_property_is_set()
    {
        assertThat(['foo' => 'bar'], set('foo'));
    }

    public function test_negated_evaluates_to_false_if_array_property_is_set()
    {
        assertThat(['foo' => 'bar'], not(notSet('foo')));
    }

    public function test_evaluates_to_true_if_class_property_is_set()
    {
        self::$_classProperty = 'bar';
        assertThat('Hamcrest\Core\SetTest', set('_classProperty'));
    }

    public function test_negated_evaluates_to_false_if_class_property_is_set()
    {
        self::$_classProperty = 'bar';
        assertThat('Hamcrest\Core\SetTest', not(notSet('_classProperty')));
    }

    public function test_evaluates_to_true_if_object_property_is_set()
    {
        $this->_instanceProperty = 'bar';
        assertThat($this, set('_instanceProperty'));
    }

    public function test_negated_evaluates_to_false_if_object_property_is_set()
    {
        $this->_instanceProperty = 'bar';
        assertThat($this, not(notSet('_instanceProperty')));
    }

    public function test_evaluates_to_false_if_array_property_is_not_set()
    {
        assertThat(['foo' => 'bar'], not(set('baz')));
    }

    public function test_negated_evaluates_to_true_if_array_property_is_not_set()
    {
        assertThat(['foo' => 'bar'], notSet('baz'));
    }

    public function test_evaluates_to_false_if_class_property_is_not_set()
    {
        assertThat('Hamcrest\Core\SetTest', not(set('_classProperty')));
    }

    public function test_negated_evaluates_to_true_if_class_property_is_not_set()
    {
        assertThat('Hamcrest\Core\SetTest', notSet('_classProperty'));
    }

    public function test_evaluates_to_false_if_object_property_is_not_set()
    {
        assertThat($this, not(set('_instanceProperty')));
    }

    public function test_negated_evaluates_to_true_if_object_property_is_not_set()
    {
        assertThat($this, notSet('_instanceProperty'));
    }

    public function test_has_a_readable_description()
    {
        $this->assertDescription('set property foo', set('foo'));
        $this->assertDescription('unset property bar', notSet('bar'));
    }

    public function test_decribes_property_setting_in_mismatch_message()
    {
        $this->assertMismatchDescription(
            'was not set',
            set('bar'),
            ['foo' => 'bar']
        );
        $this->assertMismatchDescription(
            'was "bar"',
            notSet('foo'),
            ['foo' => 'bar']
        );
        self::$_classProperty = 'bar';
        $this->assertMismatchDescription(
            'was "bar"',
            notSet('_classProperty'),
            'Hamcrest\Core\SetTest'
        );
        $this->_instanceProperty = 'bar';
        $this->assertMismatchDescription(
            'was "bar"',
            notSet('_instanceProperty'),
            $this
        );
    }
}
