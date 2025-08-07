<?php

namespace spec\Prophecy;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Prophecy\Argument\ArgumentsWildcard;
use Prophecy\Doubler\Doubler;
use Prophecy\Prophecy\MethodProphecy;
use Prophecy\Prophecy\ProphecySubjectInterface;

class ProphetSpec extends ObjectBehavior
{
    public function let(Doubler $doubler, ProphecySubjectInterface $double)
    {
        $doubler->double(null, [])->willReturn($double);

        $this->beConstructedWith($doubler);
    }

    public function it_constructs_new_prophecy_on_prophesize_call()
    {
        $prophecy = $this->prophesize();
        $prophecy->shouldBeAnInstanceOf('Prophecy\Prophecy\ObjectProphecy');
    }

    public function it_constructs_new_prophecy_with_parent_class_if_specified($doubler, ProphecySubjectInterface $newDouble)
    {
        $doubler->double(Argument::any(), [])->willReturn($newDouble);

        $this->prophesize('Prophecy\Prophet')->reveal()->shouldReturn($newDouble);
    }

    public function it_constructs_new_prophecy_with_interface_if_specified($doubler, ProphecySubjectInterface $newDouble)
    {
        $doubler->double(null, Argument::any())->willReturn($newDouble);

        $this->prophesize('ArrayAccess')->reveal()->shouldReturn($newDouble);
    }

    public function it_exposes_all_created_prophecies_through_getter()
    {
        $prophecy1 = $this->prophesize();
        $prophecy2 = $this->prophesize();

        $this->getProphecies()->shouldReturn([$prophecy1, $prophecy2]);
    }

    public function it_does_nothing_during_checkPredictions_call_if_no_predictions_defined()
    {
        $this->checkPredictions()->shouldReturn(null);
    }

    public function it_throws_AggregateException_if_defined_predictions_fail(
        MethodProphecy $method1,
        MethodProphecy $method2,
        ArgumentsWildcard $arguments1,
        ArgumentsWildcard $arguments2
    ) {
        $method1->getMethodName()->willReturn('getName');
        $method1->getArgumentsWildcard()->willReturn($arguments1);
        $method1->checkPrediction()->willReturn(null);

        $method2->getMethodName()->willReturn('isSet');
        $method2->getArgumentsWildcard()->willReturn($arguments2);
        $method2->checkPrediction()->willThrow(
            'Prophecy\Exception\Prediction\AggregateException'
        );

        $this->prophesize()->addMethodProphecy($method1);
        $this->prophesize()->addMethodProphecy($method2);

        $this->shouldThrow('Prophecy\Exception\Prediction\AggregateException')
            ->duringCheckPredictions();
    }

    public function it_exposes_doubler_through_getter($doubler)
    {
        $this->getDoubler()->shouldReturn($doubler);
    }
}
