<?php

namespace spec\Prophecy\Doubler\Generator\Node;

use PhpSpec\ObjectBehavior;

class ArgumentNodeSpec extends ObjectBehavior
{
    public function let()
    {
        $this->beConstructedWith('name');
    }

    public function it_is_not_be_passed_by_reference_by_default()
    {
        $this->shouldNotBePassedByReference();
    }

    public function it_is_passed_by_reference_if_marked()
    {
        $this->setAsPassedByReference();
        $this->shouldBePassedByReference();
    }

    public function it_is_not_variadic_by_default()
    {
        $this->shouldNotBeVariadic();
    }

    public function it_is_variadic_if_marked()
    {
        $this->setAsVariadic();
        $this->shouldBeVariadic();
    }

    public function it_does_not_have_default_by_default()
    {
        $this->shouldNotHaveDefault();
    }

    public function it_does_not_have_default_if_variadic()
    {
        $this->setDefault(null);
        $this->setAsVariadic();
        $this->shouldNotHaveDefault();
    }

    public function it_does_have_default_if_not_variadic()
    {
        $this->setDefault(null);
        $this->setAsVariadic(false);
        $this->hasDefault()->shouldReturn(true);
    }

    public function it_has_name_with_which_it_was_been_constructed()
    {
        $this->getName()->shouldReturn('name');
    }

    public function it_has_no_typehint_by_default()
    {
        $this->getTypeHint()->shouldReturn(null);
    }

    public function its_typeHint_is_mutable()
    {
        $this->setTypeHint('array');
        $this->getTypeHint()->shouldReturn('array');
    }

    public function it_does_not_have_default_value_by_default()
    {
        $this->getDefault()->shouldReturn(null);
    }

    public function it_is_not_optional_by_default()
    {
        $this->isOptional()->shouldReturn(false);
    }

    public function its_default_is_mutable()
    {
        $this->setDefault([]);
        $this->getDefault()->shouldReturn([]);
    }

    public function it_is_marked_as_optional_when_default_is_set()
    {
        $this->setDefault(null);
        $this->isOptional()->shouldReturn(true);
    }
}
