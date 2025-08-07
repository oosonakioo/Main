<?php

namespace Mockery;

class VerificationExpectation extends Expectation
{
    public function clearCountValidators()
    {
        $this->_countValidators = [];
    }

    public function __clone()
    {
        parent::__clone();
        $this->_actualCount = 0;
    }
}
