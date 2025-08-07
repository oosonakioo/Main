<?php

namespace Hamcrest\Core;

class IsInstanceOfTest extends \Hamcrest\AbstractMatcherTest
{
    private $_baseClassInstance;

    private $_subClassInstance;

    protected function setUp()
    {
        $this->_baseClassInstance = new \Hamcrest\Core\SampleBaseClass('good');
        $this->_subClassInstance = new \Hamcrest\Core\SampleSubClass('good');
    }

    protected function createMatcher()
    {
        return \Hamcrest\Core\IsInstanceOf::anInstanceOf('stdClass');
    }

    public function test_evaluates_to_true_if_argument_is_instance_of_a_specific_class()
    {
        assertThat($this->_baseClassInstance, anInstanceOf('Hamcrest\Core\SampleBaseClass'));
        assertThat($this->_subClassInstance, anInstanceOf('Hamcrest\Core\SampleSubClass'));
        assertThat(null, not(anInstanceOf('Hamcrest\Core\SampleBaseClass')));
        assertThat(new \stdClass, not(anInstanceOf('Hamcrest\Core\SampleBaseClass')));
    }

    public function test_evaluates_to_false_if_argument_is_not_an_object()
    {
        assertThat(null, not(anInstanceOf('Hamcrest\Core\SampleBaseClass')));
        assertThat(false, not(anInstanceOf('Hamcrest\Core\SampleBaseClass')));
        assertThat(5, not(anInstanceOf('Hamcrest\Core\SampleBaseClass')));
        assertThat('foo', not(anInstanceOf('Hamcrest\Core\SampleBaseClass')));
        assertThat([1, 2, 3], not(anInstanceOf('Hamcrest\Core\SampleBaseClass')));
    }

    public function test_has_a_readable_description()
    {
        $this->assertDescription('an instance of stdClass', anInstanceOf('stdClass'));
    }

    public function test_decribes_actual_class_in_mismatch_message()
    {
        $this->assertMismatchDescription(
            '[Hamcrest\Core\SampleBaseClass] <good>',
            anInstanceOf('Hamcrest\Core\SampleSubClass'),
            $this->_baseClassInstance
        );
    }
}
