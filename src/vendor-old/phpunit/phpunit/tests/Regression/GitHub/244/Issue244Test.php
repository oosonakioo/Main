<?php

class Issue244Test extends PHPUnit_Framework_TestCase
{
    /**
     * @expectedException Issue244Exception
     *
     * @expectedExceptionCode 123StringCode
     */
    public function test_works()
    {
        throw new Issue244Exception;
    }

    /**
     * @expectedException Issue244Exception
     *
     * @expectedExceptionCode OtherString
     */
    public function test_fails()
    {
        throw new Issue244Exception;
    }

    /**
     * @expectedException Issue244Exception
     *
     * @expectedExceptionCode 123
     */
    public function test_fails_too_if_expectation_is_a_number()
    {
        throw new Issue244Exception;
    }

    /**
     * @expectedException Issue244ExceptionIntCode
     *
     * @expectedExceptionCode 123String
     */
    public function test_fails_too_if_exception_code_is_a_number()
    {
        throw new Issue244ExceptionIntCode;
    }
}

class Issue244Exception extends Exception
{
    public function __construct()
    {
        $this->code = '123StringCode';
    }
}

class Issue244ExceptionIntCode extends Exception
{
    public function __construct()
    {
        $this->code = 123;
    }
}
