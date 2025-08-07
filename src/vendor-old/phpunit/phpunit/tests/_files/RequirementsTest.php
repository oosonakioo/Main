<?php

class RequirementsTest extends PHPUnit_Framework_TestCase
{
    public function test_one() {}

    /**
     * @requires PHPUnit 1.0
     */
    public function test_two() {}

    /**
     * @requires PHP 2.0
     */
    public function test_three() {}

    /**
     * @requires PHPUnit 2.0
     * @requires PHP 1.0
     */
    public function test_four() {}

    /**
     * @requires PHP 5.4.0RC6
     */
    public function test_five() {}

    /**
     * @requires PHP 5.4.0-alpha1
     */
    public function test_six() {}

    /**
     * @requires PHP 5.4.0beta2
     */
    public function test_seven() {}

    /**
     * @requires PHP 5.4-dev
     */
    public function test_eight() {}

    /**
     * @requires function testFunc
     */
    public function test_nine() {}

    /**
     * @requires extension testExt
     */
    public function test_ten() {}

    /**
     * @requires OS Linux
     */
    public function test_eleven() {}

    /**
     * @requires PHP 99-dev
     * @requires PHPUnit 9-dev
     * @requires OS DOESNOTEXIST
     * @requires function testFuncOne
     * @requires function testFuncTwo
     * @requires extension testExtOne
     * @requires extension testExtTwo
     */
    public function test_all_possible_requirements() {}

    /**
     * @requires function array_merge
     */
    public function test_existing_function() {}

    /**
     * @requires function ReflectionMethod::setAccessible
     */
    public function test_existing_method() {}

    /**
     * @requires extension spl
     */
    public function test_existing_extension() {}

    /**
     * @requires OS .*
     */
    public function test_existing_os() {}

    /**
     * @requires PHPUnit 1111111
     */
    public function test_always_skip() {}

    /**
     * @requires PHP 9999999
     */
    public function test_always_skip2() {}

    /**
     * @requires OS DOESNOTEXIST
     */
    public function test_always_skip3() {}

    /**
     * @requires	  extension	  spl
     * @requires	  OS	  .*
     */
    public function test_space() {}
}
