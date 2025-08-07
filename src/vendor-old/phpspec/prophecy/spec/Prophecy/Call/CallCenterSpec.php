<?php

namespace spec\Prophecy\Call;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument\ArgumentsWildcard;
use Prophecy\Promise\PromiseInterface;
use Prophecy\Prophecy\MethodProphecy;
use Prophecy\Prophecy\ObjectProphecy;

class CallCenterSpec extends ObjectBehavior
{
    public function let(ObjectProphecy $objectProphecy) {}

    public function it_records_calls_made_through_makeCall_method(ObjectProphecy $objectProphecy, ArgumentsWildcard $wildcard)
    {
        $wildcard->scoreArguments([5, 2, 3])->willReturn(10);
        $objectProphecy->getMethodProphecies()->willReturn([]);

        $this->makeCall($objectProphecy, 'setValues', [5, 2, 3]);

        $calls = $this->findCalls('setValues', $wildcard);
        $calls->shouldHaveCount(1);

        $calls[0]->shouldBeAnInstanceOf('Prophecy\Call\Call');
        $calls[0]->getMethodName()->shouldReturn('setValues');
        $calls[0]->getArguments()->shouldReturn([5, 2, 3]);
        $calls[0]->getReturnValue()->shouldReturn(null);
    }

    public function it_returns_null_for_any_call_through_makeCall_if_no_method_prophecies_added(
        $objectProphecy
    ) {
        $objectProphecy->getMethodProphecies()->willReturn([]);

        $this->makeCall($objectProphecy, 'setValues', [5, 2, 3])->shouldReturn(null);
    }

    public function it_executes_promise_of_method_prophecy_that_matches_signature_passed_to_makeCall(
        $objectProphecy,
        MethodProphecy $method1,
        MethodProphecy $method2,
        MethodProphecy $method3,
        ArgumentsWildcard $arguments1,
        ArgumentsWildcard $arguments2,
        ArgumentsWildcard $arguments3,
        PromiseInterface $promise
    ) {
        $method1->hasReturnVoid()->willReturn(false);
        $method1->getMethodName()->willReturn('getName');
        $method1->getArgumentsWildcard()->willReturn($arguments1);
        $arguments1->scoreArguments(['world', 'everything'])->willReturn(false);

        $method2->hasReturnVoid()->willReturn(false);
        $method2->getMethodName()->willReturn('setTitle');
        $method2->getArgumentsWildcard()->willReturn($arguments2);
        $arguments2->scoreArguments(['world', 'everything'])->willReturn(false);

        $method3->hasReturnVoid()->willReturn(false);
        $method3->getMethodName()->willReturn('getName');
        $method3->getArgumentsWildcard()->willReturn($arguments3);
        $method3->getPromise()->willReturn($promise);
        $arguments3->scoreArguments(['world', 'everything'])->willReturn(200);

        $objectProphecy->getMethodProphecies()->willReturn([
            'method1' => [$method1],
            'method2' => [$method2, $method3],
        ]);
        $objectProphecy->getMethodProphecies('getName')->willReturn([$method1, $method3]);
        $objectProphecy->reveal()->willReturn(new \stdClass);

        $promise->execute(['world', 'everything'], $objectProphecy->getWrappedObject(), $method3)->willReturn(42);

        $this->makeCall($objectProphecy, 'getName', ['world', 'everything'])->shouldReturn(42);

        $calls = $this->findCalls('getName', $arguments3);
        $calls->shouldHaveCount(1);
        $calls[0]->getReturnValue()->shouldReturn(42);
    }

    public function it_executes_promise_of_method_prophecy_that_matches_with_highest_score_to_makeCall(
        $objectProphecy,
        MethodProphecy $method1,
        MethodProphecy $method2,
        MethodProphecy $method3,
        ArgumentsWildcard $arguments1,
        ArgumentsWildcard $arguments2,
        ArgumentsWildcard $arguments3,
        PromiseInterface $promise
    ) {
        $method1->hasReturnVoid()->willReturn(false);
        $method1->getMethodName()->willReturn('getName');
        $method1->getArgumentsWildcard()->willReturn($arguments1);
        $arguments1->scoreArguments(['world', 'everything'])->willReturn(50);

        $method2->hasReturnVoid()->willReturn(false);
        $method2->getMethodName()->willReturn('getName');
        $method2->getArgumentsWildcard()->willReturn($arguments2);
        $method2->getPromise()->willReturn($promise);
        $arguments2->scoreArguments(['world', 'everything'])->willReturn(300);

        $method3->hasReturnVoid()->willReturn(false);
        $method3->getMethodName()->willReturn('getName');
        $method3->getArgumentsWildcard()->willReturn($arguments3);
        $arguments3->scoreArguments(['world', 'everything'])->willReturn(200);

        $objectProphecy->getMethodProphecies()->willReturn([
            'method1' => [$method1],
            'method2' => [$method2, $method3],
        ]);
        $objectProphecy->getMethodProphecies('getName')->willReturn([
            $method1, $method2, $method3,
        ]);
        $objectProphecy->reveal()->willReturn(new \stdClass);

        $promise->execute(['world', 'everything'], $objectProphecy->getWrappedObject(), $method2)
            ->willReturn('second');

        $this->makeCall($objectProphecy, 'getName', ['world', 'everything'])
            ->shouldReturn('second');
    }

    public function it_throws_exception_if_call_does_not_match_any_of_defined_method_prophecies(
        $objectProphecy,
        MethodProphecy $method,
        ArgumentsWildcard $arguments
    ) {
        $method->getMethodName()->willReturn('getName');
        $method->getArgumentsWildcard()->willReturn($arguments);
        $arguments->scoreArguments(['world', 'everything'])->willReturn(false);
        $arguments->__toString()->willReturn('arg1, arg2');

        $objectProphecy->getMethodProphecies()->willReturn(['method1' => [$method]]);
        $objectProphecy->getMethodProphecies('getName')->willReturn([$method]);

        $this->shouldThrow('Prophecy\Exception\Call\UnexpectedCallException')
            ->duringMakeCall($objectProphecy, 'getName', ['world', 'everything']);
    }

    public function it_returns_null_if_method_prophecy_that_matches_makeCall_arguments_has_no_promise(
        $objectProphecy,
        MethodProphecy $method,
        ArgumentsWildcard $arguments
    ) {
        $method->hasReturnVoid()->willReturn(false);
        $method->getMethodName()->willReturn('getName');
        $method->getArgumentsWildcard()->willReturn($arguments);
        $method->getPromise()->willReturn(null);
        $arguments->scoreArguments(['world', 'everything'])->willReturn(100);

        $objectProphecy->getMethodProphecies()->willReturn([$method]);
        $objectProphecy->getMethodProphecies('getName')->willReturn([$method]);

        $this->makeCall($objectProphecy, 'getName', ['world', 'everything'])
            ->shouldReturn(null);
    }

    public function it_finds_recorded_calls_by_a_method_name_and_arguments_wildcard(
        $objectProphecy,
        ArgumentsWildcard $wildcard
    ) {
        $objectProphecy->getMethodProphecies()->willReturn([]);

        $this->makeCall($objectProphecy, 'getName', ['world']);
        $this->makeCall($objectProphecy, 'getName', ['everything']);
        $this->makeCall($objectProphecy, 'setName', [42]);

        $wildcard->scoreArguments(['world'])->willReturn(false);
        $wildcard->scoreArguments(['everything'])->willReturn(10);

        $calls = $this->findCalls('getName', $wildcard);

        $calls->shouldHaveCount(1);
        $calls[0]->getMethodName()->shouldReturn('getName');
        $calls[0]->getArguments()->shouldReturn(['everything']);
    }
}
