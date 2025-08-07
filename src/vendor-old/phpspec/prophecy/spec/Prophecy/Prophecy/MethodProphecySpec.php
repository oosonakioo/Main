<?php

namespace spec\Prophecy\Prophecy;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument\ArgumentsWildcard;
use Prophecy\Call\Call;
use Prophecy\Prediction\PredictionInterface;
use Prophecy\Promise\PromiseInterface;
use Prophecy\Prophecy\ObjectProphecy;

class ClassWithFinalMethod
{
    final public function finalMethod() {}
}

class MethodProphecySpec extends ObjectBehavior
{
    public function let(ObjectProphecy $objectProphecy, \ReflectionClass $reflection)
    {
        $objectProphecy->reveal()->willReturn($reflection);

        $this->beConstructedWith($objectProphecy, 'getName', null);
    }

    public function it_is_initializable()
    {
        $this->shouldHaveType('Prophecy\Prophecy\MethodProphecy');
    }

    public function its_constructor_throws_MethodNotFoundException_for_unexisting_method($objectProphecy)
    {
        $this->shouldThrow('Prophecy\Exception\Doubler\MethodNotFoundException')->during(
            '__construct', [$objectProphecy, 'getUnexisting', null]
        );
    }

    public function its_constructor_throws_MethodProphecyException_for_final_methods($objectProphecy, ClassWithFinalMethod $subject)
    {
        $objectProphecy->reveal()->willReturn($subject);

        $this->shouldThrow('Prophecy\Exception\Prophecy\MethodProphecyException')->during(
            '__construct', [$objectProphecy, 'finalMethod', null]
        );
    }

    public function its_constructor_transforms_array_passed_as_3rd_argument_to_ArgumentsWildcard(
        $objectProphecy
    ) {
        $this->beConstructedWith($objectProphecy, 'getName', [42, 33]);

        $wildcard = $this->getArgumentsWildcard();
        $wildcard->shouldNotBe(null);
        $wildcard->__toString()->shouldReturn('exact(42), exact(33)');
    }

    public function its_constructor_does_not_touch_third_argument_if_it_is_null($objectProphecy)
    {
        $this->beConstructedWith($objectProphecy, 'getName', null);

        $wildcard = $this->getArgumentsWildcard();
        $wildcard->shouldBe(null);
    }

    public function it_records_promise_through_will_method(PromiseInterface $promise, $objectProphecy)
    {
        $objectProphecy->addMethodProphecy($this)->willReturn(null);

        $this->will($promise);
        $this->getPromise()->shouldReturn($promise);
    }

    public function it_adds_itself_to_ObjectProphecy_during_call_to_will(PromiseInterface $objectProphecy, $promise)
    {
        $objectProphecy->addMethodProphecy($this)->shouldBeCalled();

        $this->will($promise);
    }

    public function it_adds_ReturnPromise_during_willReturn_call($objectProphecy)
    {
        $objectProphecy->addMethodProphecy($this)->willReturn(null);

        $this->willReturn(42);
        $this->getPromise()->shouldBeAnInstanceOf('Prophecy\Promise\ReturnPromise');
    }

    public function it_adds_ThrowPromise_during_willThrow_call($objectProphecy)
    {
        $objectProphecy->addMethodProphecy($this)->willReturn(null);

        $this->willThrow('RuntimeException');
        $this->getPromise()->shouldBeAnInstanceOf('Prophecy\Promise\ThrowPromise');
    }

    public function it_adds_ReturnArgumentPromise_during_willReturnArgument_call($objectProphecy)
    {
        $objectProphecy->addMethodProphecy($this)->willReturn(null);

        $this->willReturnArgument();
        $this->getPromise()->shouldBeAnInstanceOf('Prophecy\Promise\ReturnArgumentPromise');
    }

    public function it_adds_ReturnArgumentPromise_during_willReturnArgument_call_with_index_argument($objectProphecy)
    {
        $objectProphecy->addMethodProphecy($this)->willReturn(null);

        $this->willReturnArgument(1);
        $promise = $this->getPromise();
        $promise->shouldBeAnInstanceOf('Prophecy\Promise\ReturnArgumentPromise');
        $promise->execute(['one', 'two'], $objectProphecy, $this)->shouldReturn('two');
    }

    public function it_adds_CallbackPromise_during_will_call_with_callback_argument($objectProphecy)
    {
        $objectProphecy->addMethodProphecy($this)->willReturn(null);

        $callback = function () {};

        $this->will($callback);
        $this->getPromise()->shouldBeAnInstanceOf('Prophecy\Promise\CallbackPromise');
    }

    public function it_records_prediction_through_should_method(PredictionInterface $prediction, $objectProphecy)
    {
        $objectProphecy->addMethodProphecy($this)->willReturn(null);

        $this->callOnWrappedObject('should', [$prediction]);
        $this->getPrediction()->shouldReturn($prediction);
    }

    public function it_adds_CallbackPrediction_during_should_call_with_callback_argument($objectProphecy)
    {
        $objectProphecy->addMethodProphecy($this)->willReturn(null);

        $callback = function () {};

        $this->callOnWrappedObject('should', [$callback]);
        $this->getPrediction()->shouldBeAnInstanceOf('Prophecy\Prediction\CallbackPrediction');
    }

    public function it_adds_itself_to_ObjectProphecy_during_call_to_should($objectProphecy, PredictionInterface $prediction)
    {
        $objectProphecy->addMethodProphecy($this)->shouldBeCalled();

        $this->callOnWrappedObject('should', [$prediction]);
    }

    public function it_adds_CallPrediction_during_shouldBeCalled_call($objectProphecy)
    {
        $objectProphecy->addMethodProphecy($this)->willReturn(null);

        $this->callOnWrappedObject('shouldBeCalled', []);
        $this->getPrediction()->shouldBeAnInstanceOf('Prophecy\Prediction\CallPrediction');
    }

    public function it_adds_NoCallsPrediction_during_shouldNotBeCalled_call($objectProphecy)
    {
        $objectProphecy->addMethodProphecy($this)->willReturn(null);

        $this->callOnWrappedObject('shouldNotBeCalled', []);
        $this->getPrediction()->shouldBeAnInstanceOf('Prophecy\Prediction\NoCallsPrediction');
    }

    public function it_adds_CallTimesPrediction_during_shouldBeCalledTimes_call($objectProphecy)
    {
        $objectProphecy->addMethodProphecy($this)->willReturn(null);

        $this->callOnWrappedObject('shouldBeCalledTimes', [5]);
        $this->getPrediction()->shouldBeAnInstanceOf('Prophecy\Prediction\CallTimesPrediction');
    }

    public function it_checks_prediction_via_shouldHave_method_call(
        $objectProphecy,
        ArgumentsWildcard $arguments,
        PredictionInterface $prediction,
        Call $call1,
        Call $call2
    ) {
        $objectProphecy->addMethodProphecy($this)->willReturn(null);
        $prediction->check([$call1, $call2], $objectProphecy->getWrappedObject(), $this)->shouldBeCalled();
        $objectProphecy->findProphecyMethodCalls('getName', $arguments)->willReturn([$call1, $call2]);

        $this->withArguments($arguments);
        $this->callOnWrappedObject('shouldHave', [$prediction]);
    }

    public function it_sets_return_promise_during_shouldHave_call_if_none_was_set_before(
        $objectProphecy,
        ArgumentsWildcard $arguments,
        PredictionInterface $prediction,
        Call $call1,
        Call $call2
    ) {
        $objectProphecy->addMethodProphecy($this)->willReturn(null);
        $prediction->check([$call1, $call2], $objectProphecy->getWrappedObject(), $this)->shouldBeCalled();
        $objectProphecy->findProphecyMethodCalls('getName', $arguments)->willReturn([$call1, $call2]);

        $this->withArguments($arguments);
        $this->callOnWrappedObject('shouldHave', [$prediction]);

        $this->getPromise()->shouldReturnAnInstanceOf('Prophecy\Promise\ReturnPromise');
    }

    public function it_does_not_set_return_promise_during_shouldHave_call_if_it_was_set_before(
        $objectProphecy,
        ArgumentsWildcard $arguments,
        PredictionInterface $prediction,
        Call $call1,
        Call $call2,
        PromiseInterface $promise
    ) {
        $objectProphecy->addMethodProphecy($this)->willReturn(null);
        $prediction->check([$call1, $call2], $objectProphecy->getWrappedObject(), $this)->shouldBeCalled();
        $objectProphecy->findProphecyMethodCalls('getName', $arguments)->willReturn([$call1, $call2]);

        $this->will($promise);
        $this->withArguments($arguments);
        $this->callOnWrappedObject('shouldHave', [$prediction]);

        $this->getPromise()->shouldReturn($promise);
    }

    public function it_records_checked_predictions(
        $objectProphecy,
        ArgumentsWildcard $arguments,
        PredictionInterface $prediction1,
        PredictionInterface $prediction2,
        Call $call1,
        Call $call2,
        PromiseInterface $promise
    ) {
        $objectProphecy->addMethodProphecy($this)->willReturn(null);
        $prediction1->check([$call1, $call2], $objectProphecy->getWrappedObject(), $this)->willReturn();
        $prediction2->check([$call1, $call2], $objectProphecy->getWrappedObject(), $this)->willReturn();
        $objectProphecy->findProphecyMethodCalls('getName', $arguments)->willReturn([$call1, $call2]);

        $this->will($promise);
        $this->withArguments($arguments);
        $this->callOnWrappedObject('shouldHave', [$prediction1]);
        $this->callOnWrappedObject('shouldHave', [$prediction2]);

        $this->getCheckedPredictions()->shouldReturn([$prediction1, $prediction2]);
    }

    public function it_records_even_failed_checked_predictions(
        $objectProphecy,
        ArgumentsWildcard $arguments,
        PredictionInterface $prediction,
        Call $call1,
        Call $call2,
        PromiseInterface $promise
    ) {
        $objectProphecy->addMethodProphecy($this)->willReturn(null);
        $prediction->check([$call1, $call2], $objectProphecy->getWrappedObject(), $this)->willThrow(new \RuntimeException);
        $objectProphecy->findProphecyMethodCalls('getName', $arguments)->willReturn([$call1, $call2]);

        $this->will($promise);
        $this->withArguments($arguments);

        try {
            $this->callOnWrappedObject('shouldHave', [$prediction]);
        } catch (\Exception $e) {
        }

        $this->getCheckedPredictions()->shouldReturn([$prediction]);
    }

    public function it_checks_prediction_via_shouldHave_method_call_with_callback(
        $objectProphecy,
        ArgumentsWildcard $arguments,
        Call $call1,
        Call $call2
    ) {
        $callback = function ($calls, $object, $method) {
            throw new \RuntimeException;
        };
        $objectProphecy->findProphecyMethodCalls('getName', $arguments)->willReturn([$call1, $call2]);

        $this->withArguments($arguments);
        $this->shouldThrow('RuntimeException')->duringShouldHave($callback);
    }

    public function it_does_nothing_during_checkPrediction_if_no_prediction_set()
    {
        $this->checkPrediction()->shouldReturn(null);
    }

    public function it_checks_set_prediction_during_checkPrediction(
        $objectProphecy,
        ArgumentsWildcard $arguments,
        PredictionInterface $prediction,
        Call $call1,
        Call $call2
    ) {
        $prediction->check([$call1, $call2], $objectProphecy->getWrappedObject(), $this)->shouldBeCalled();
        $objectProphecy->findProphecyMethodCalls('getName', $arguments)->willReturn([$call1, $call2]);
        $objectProphecy->addMethodProphecy($this)->willReturn(null);

        $this->withArguments($arguments);
        $this->callOnWrappedObject('should', [$prediction]);
        $this->checkPrediction();
    }

    public function it_links_back_to_ObjectProphecy_through_getter($objectProphecy)
    {
        $this->getObjectProphecy()->shouldReturn($objectProphecy);
    }

    public function it_has_MethodName()
    {
        $this->getMethodName()->shouldReturn('getName');
    }

    public function it_contains_ArgumentsWildcard_it_was_constructed_with($objectProphecy, ArgumentsWildcard $wildcard)
    {
        $this->beConstructedWith($objectProphecy, 'getName', $wildcard);

        $this->getArgumentsWildcard()->shouldReturn($wildcard);
    }

    public function its_ArgumentWildcard_is_mutable_through_setter(ArgumentsWildcard $wildcard)
    {
        $this->withArguments($wildcard);

        $this->getArgumentsWildcard()->shouldReturn($wildcard);
    }

    public function its_withArguments_transforms_passed_array_into_ArgumentsWildcard()
    {
        $this->withArguments([42, 33]);

        $wildcard = $this->getArgumentsWildcard();
        $wildcard->shouldNotBe(null);
        $wildcard->__toString()->shouldReturn('exact(42), exact(33)');
    }

    public function its_withArguments_throws_exception_if_wrong_arguments_provided()
    {
        $this->shouldThrow('Prophecy\Exception\InvalidArgumentException')->duringWithArguments(42);
    }
}
