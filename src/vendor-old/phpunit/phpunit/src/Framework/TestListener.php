<?php

/*
 * This file is part of PHPUnit.
 *
 * (c) Sebastian Bergmann <sebastian@phpunit.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * A Listener for test progress.
 *
 * @since Interface available since Release 2.0.0
 */
interface PHPUnit_Framework_TestListener
{
    /**
     * An error occurred.
     *
     * @param  float  $time
     */
    public function addError(PHPUnit_Framework_Test $test, Exception $e, $time);

    /**
     * A failure occurred.
     *
     * @param  float  $time
     */
    public function addFailure(PHPUnit_Framework_Test $test, PHPUnit_Framework_AssertionFailedError $e, $time);

    /**
     * Incomplete test.
     *
     * @param  float  $time
     */
    public function addIncompleteTest(PHPUnit_Framework_Test $test, Exception $e, $time);

    /**
     * Risky test.
     *
     * @param  float  $time
     *
     * @since  Method available since Release 4.0.0
     */
    public function addRiskyTest(PHPUnit_Framework_Test $test, Exception $e, $time);

    /**
     * Skipped test.
     *
     * @param  float  $time
     *
     * @since  Method available since Release 3.0.0
     */
    public function addSkippedTest(PHPUnit_Framework_Test $test, Exception $e, $time);

    /**
     * A test suite started.
     *
     *
     * @since  Method available since Release 2.2.0
     */
    public function startTestSuite(PHPUnit_Framework_TestSuite $suite);

    /**
     * A test suite ended.
     *
     *
     * @since  Method available since Release 2.2.0
     */
    public function endTestSuite(PHPUnit_Framework_TestSuite $suite);

    /**
     * A test started.
     */
    public function startTest(PHPUnit_Framework_Test $test);

    /**
     * A test ended.
     *
     * @param  float  $time
     */
    public function endTest(PHPUnit_Framework_Test $test, $time);
}
