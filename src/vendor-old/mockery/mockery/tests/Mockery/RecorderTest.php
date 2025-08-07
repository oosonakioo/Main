<?php

/**
 * Mockery
 *
 * LICENSE
 *
 * This source file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://github.com/padraic/mockery/master/LICENSE
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to padraic@php.net so we can send you a copy immediately.
 *
 * @category   Mockery
 *
 * @copyright  Copyright (c) 2010-2014 Pádraic Brady (http://blog.astrumfutura.com)
 * @license    http://github.com/padraic/mockery/blob/master/LICENSE New BSD License
 */

use Mockery\Adapter\Phpunit\MockeryTestCase;

class RecorderTest extends MockeryTestCase
{
    protected function setup()
    {
        $this->container = new \Mockery\Container(\Mockery::getDefaultGenerator(), \Mockery::getDefaultLoader());
    }

    protected function teardown()
    {
        $this->container->mockery_close();
    }

    public function test_recorder_with_simple_object()
    {
        $mock = $this->container->mock(new MockeryTestSubject);
        $mock->shouldExpect(function ($subject) {
            $user = new MockeryTestSubjectUser($subject);
            $user->doFoo();
        });

        $this->assertEquals(1, $mock->foo());
        $mock->mockery_verify();
    }

    public function test_arguments_are_passed_as_method_expectations()
    {
        $mock = $this->container->mock(new MockeryTestSubject);
        $mock->shouldExpect(function ($subject) {
            $user = new MockeryTestSubjectUser($subject);
            $user->doBar();
        });

        $this->assertEquals(4, $mock->bar(2));
        $mock->mockery_verify();
    }

    public function test_arguments_loosely_matched_by_default()
    {
        $mock = $this->container->mock(new MockeryTestSubject);
        $mock->shouldExpect(function ($subject) {
            $user = new MockeryTestSubjectUser($subject);
            $user->doBar();
        });

        $this->assertEquals(4, $mock->bar('2'));
        $mock->mockery_verify();
    }

    public function test_multiple_method_expectations()
    {
        $mock = $this->container->mock(new MockeryTestSubject);
        $mock->shouldExpect(function ($subject) {
            $user = new MockeryTestSubjectUser($subject);
            $user->doFoo();
            $user->doBar();
        });

        $this->assertEquals(1, $mock->foo());
        $this->assertEquals(4, $mock->bar(2));
        $mock->mockery_verify();
    }

    public function test_recording_does_not_specify_exact_order_by_default()
    {
        $mock = $this->container->mock(new MockeryTestSubject);
        $mock->shouldExpect(function ($subject) {
            $user = new MockeryTestSubjectUser($subject);
            $user->doFoo();
            $user->doBar();
        });

        $this->assertEquals(4, $mock->bar(2));
        $this->assertEquals(1, $mock->foo());
        $mock->mockery_verify();
    }

    /**
     * @expectedException \Mockery\Exception
     */
    public function test_recording_does_specify_exact_order_in_strict_mode()
    {
        $mock = $this->container->mock(new MockeryTestSubject);
        $mock->shouldExpect(function ($subject) {
            $subject->shouldBeStrict();
            $user = new MockeryTestSubjectUser($subject);
            $user->doFoo();
            $user->doBar();
        });

        $mock->bar(2);
        $mock->foo();
        $mock->mockery_verify();
    }

    /**
     * @expectedException \Mockery\Exception
     */
    public function test_arguments_are_matched_exactly_under_strict_mode()
    {
        $mock = $this->container->mock(new MockeryTestSubject);
        $mock->shouldExpect(function ($subject) {
            $subject->shouldBeStrict();
            $user = new MockeryTestSubjectUser($subject);
            $user->doBar();
        });

        $mock->bar('2');
    }

    /**
     * @expectedException \Mockery\Exception
     */
    public function test_throws_exception_when_arguments_not_expected()
    {
        $mock = $this->container->mock(new MockeryTestSubject);
        $mock->shouldExpect(function ($subject) {
            $user = new MockeryTestSubjectUser($subject);
            $user->doBar();
        });

        $mock->bar(4);
    }

    public function test_call_count_unconstrained_by_default()
    {
        $mock = $this->container->mock(new MockeryTestSubject);
        $mock->shouldExpect(function ($subject) {
            $user = new MockeryTestSubjectUser($subject);
            $user->doBar();
        });

        $mock->bar(2);
        $this->assertEquals(4, $mock->bar(2));
        $mock->mockery_verify();
    }

    /**
     * @expectedException \Mockery\CountValidator\Exception
     */
    public function test_call_count_constrained_in_strict_mode()
    {
        $mock = $this->container->mock(new MockeryTestSubject);
        $mock->shouldExpect(function ($subject) {
            $subject->shouldBeStrict();
            $user = new MockeryTestSubjectUser($subject);
            $user->doBar();
        });

        $mock->bar(2);
        $mock->bar(2);
        $mock->mockery_verify();
    }
}

class MockeryTestSubject
{
    public function foo()
    {
        return 1;
    }

    public function bar($i)
    {
        return $i * 2;
    }
}

class MockeryTestSubjectUser
{
    public $subject = null;

    public function __construct($subject)
    {
        $this->subject = $subject;
    }

    public function doFoo()
    {
        return $this->subject->foo();
    }

    public function doBar()
    {
        return $this->subject->bar(2);
    }
}
