<?php

namespace Mockery;

class VerificationDirector
{
    private $receivedMethodCalls;

    private $expectation;

    public function __construct(ReceivedMethodCalls $receivedMethodCalls, VerificationExpectation $expectation)
    {
        $this->receivedMethodCalls = $receivedMethodCalls;
        $this->expectation = $expectation;
    }

    public function verify()
    {
        return $this->receivedMethodCalls->verify($this->expectation);
    }

    public function with()
    {
        return $this->cloneApplyAndVerify('with', func_get_args());
    }

    public function withArgs(array $args)
    {
        return $this->cloneApplyAndVerify('withArgs', [$args]);
    }

    public function withNoArgs()
    {
        return $this->cloneApplyAndVerify('withNoArgs', []);
    }

    public function withAnyArgs()
    {
        return $this->cloneApplyAndVerify('withAnyArgs', []);
    }

    public function times($limit = null)
    {
        return $this->cloneWithoutCountValidatorsApplyAndVerify('times', [$limit]);
    }

    public function once()
    {
        return $this->cloneWithoutCountValidatorsApplyAndVerify('once', []);
    }

    public function twice()
    {
        return $this->cloneWithoutCountValidatorsApplyAndVerify('twice', []);
    }

    public function atLeast()
    {
        return $this->cloneWithoutCountValidatorsApplyAndVerify('atLeast', []);
    }

    public function atMost()
    {
        return $this->cloneWithoutCountValidatorsApplyAndVerify('atMost', []);
    }

    public function between($minimum, $maximum)
    {
        return $this->cloneWithoutCountValidatorsApplyAndVerify('between', [$minimum, $maximum]);
    }

    protected function cloneWithoutCountValidatorsApplyAndVerify($method, $args)
    {
        $expectation = clone $this->expectation;
        $expectation->clearCountValidators();
        call_user_func_array([$expectation, $method], $args);
        $director = new VerificationDirector($this->receivedMethodCalls, $expectation);
        $director->verify();

        return $director;
    }

    protected function cloneApplyAndVerify($method, $args)
    {
        $expectation = clone $this->expectation;
        call_user_func_array([$expectation, $method], $args);
        $director = new VerificationDirector($this->receivedMethodCalls, $expectation);
        $director->verify();

        return $director;
    }
}
