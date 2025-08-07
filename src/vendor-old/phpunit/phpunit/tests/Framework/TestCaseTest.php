<?php

/*
 * This file is part of PHPUnit.
 *
 * (c) Sebastian Bergmann <sebastian@phpunit.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

require_once dirname(__DIR__).DIRECTORY_SEPARATOR.'_files'.DIRECTORY_SEPARATOR.'NoArgTestCaseTest.php';
require_once dirname(__DIR__).DIRECTORY_SEPARATOR.'_files'.DIRECTORY_SEPARATOR.'Singleton.php';

$GLOBALS['a'] = 'a';
$_ENV['b'] = 'b';
$_POST['c'] = 'c';
$_GET['d'] = 'd';
$_COOKIE['e'] = 'e';
$_SERVER['f'] = 'f';
$_FILES['g'] = 'g';
$_REQUEST['h'] = 'h';
$GLOBALS['i'] = 'i';

/**
 * @since      Class available since Release 2.0.0
 *
 * @covers     PHPUnit_Framework_TestCase
 */
class Framework_TestCaseTest extends PHPUnit_Framework_TestCase
{
    protected $backupGlobalsBlacklist = ['i', 'singleton'];

    /**
     * Used be testStaticAttributesBackupPre
     */
    protected static $_testStatic = 0;

    public function test_case_to_string()
    {
        $this->assertEquals(
            'Framework_TestCaseTest::testCaseToString',
            $this->toString()
        );
    }

    public function test_success()
    {
        $test = new Success;
        $result = $test->run();

        $this->assertEquals(PHPUnit_Runner_BaseTestRunner::STATUS_PASSED, $test->getStatus());
        $this->assertEquals(0, $result->errorCount());
        $this->assertEquals(0, $result->failureCount());
        $this->assertEquals(0, $result->skippedCount());
        $this->assertEquals(1, count($result));
    }

    public function test_failure()
    {
        $test = new Failure;
        $result = $test->run();

        $this->assertEquals(PHPUnit_Runner_BaseTestRunner::STATUS_FAILURE, $test->getStatus());
        $this->assertEquals(0, $result->errorCount());
        $this->assertEquals(1, $result->failureCount());
        $this->assertEquals(0, $result->skippedCount());
        $this->assertEquals(1, count($result));
    }

    public function test_error()
    {
        $test = new TestError;
        $result = $test->run();

        $this->assertEquals(PHPUnit_Runner_BaseTestRunner::STATUS_ERROR, $test->getStatus());
        $this->assertEquals(1, $result->errorCount());
        $this->assertEquals(0, $result->failureCount());
        $this->assertEquals(0, $result->skippedCount());
        $this->assertEquals(1, count($result));
    }

    public function test_skipped()
    {
        $test = new TestSkipped;
        $result = $test->run();

        $this->assertEquals(PHPUnit_Runner_BaseTestRunner::STATUS_SKIPPED, $test->getStatus());
        $this->assertEquals('Skipped test', $test->getStatusMessage());
        $this->assertEquals(0, $result->errorCount());
        $this->assertEquals(0, $result->failureCount());
        $this->assertEquals(1, $result->skippedCount());
        $this->assertEquals(1, count($result));
    }

    public function test_incomplete()
    {
        $test = new TestIncomplete;
        $result = $test->run();

        $this->assertEquals(PHPUnit_Runner_BaseTestRunner::STATUS_INCOMPLETE, $test->getStatus());
        $this->assertEquals('Incomplete test', $test->getStatusMessage());
        $this->assertEquals(0, $result->errorCount());
        $this->assertEquals(0, $result->failureCount());
        $this->assertEquals(0, $result->skippedCount());
        $this->assertEquals(1, count($result));
    }

    public function test_exception_in_set_up()
    {
        $test = new ExceptionInSetUpTest('testSomething');
        $result = $test->run();

        $this->assertTrue($test->setUp);
        $this->assertFalse($test->assertPreConditions);
        $this->assertFalse($test->testSomething);
        $this->assertFalse($test->assertPostConditions);
        $this->assertTrue($test->tearDown);
    }

    public function test_exception_in_assert_pre_conditions()
    {
        $test = new ExceptionInAssertPreConditionsTest('testSomething');
        $result = $test->run();

        $this->assertTrue($test->setUp);
        $this->assertTrue($test->assertPreConditions);
        $this->assertFalse($test->testSomething);
        $this->assertFalse($test->assertPostConditions);
        $this->assertTrue($test->tearDown);
    }

    public function test_exception_in_test()
    {
        $test = new ExceptionInTest('testSomething');
        $result = $test->run();

        $this->assertTrue($test->setUp);
        $this->assertTrue($test->assertPreConditions);
        $this->assertTrue($test->testSomething);
        $this->assertFalse($test->assertPostConditions);
        $this->assertTrue($test->tearDown);
    }

    public function test_exception_in_assert_post_conditions()
    {
        $test = new ExceptionInAssertPostConditionsTest('testSomething');
        $result = $test->run();

        $this->assertTrue($test->setUp);
        $this->assertTrue($test->assertPreConditions);
        $this->assertTrue($test->testSomething);
        $this->assertTrue($test->assertPostConditions);
        $this->assertTrue($test->tearDown);
    }

    public function test_exception_in_tear_down()
    {
        $test = new ExceptionInTearDownTest('testSomething');
        $result = $test->run();

        $this->assertTrue($test->setUp);
        $this->assertTrue($test->assertPreConditions);
        $this->assertTrue($test->testSomething);
        $this->assertTrue($test->assertPostConditions);
        $this->assertTrue($test->tearDown);
    }

    public function test_no_arg_test_case_passes()
    {
        $result = new PHPUnit_Framework_TestResult;
        $t = new PHPUnit_Framework_TestSuite('NoArgTestCaseTest');

        $t->run($result);

        $this->assertEquals(1, count($result));
        $this->assertEquals(0, $result->failureCount());
        $this->assertEquals(0, $result->errorCount());
    }

    public function test_was_run()
    {
        $test = new WasRun;
        $test->run();

        $this->assertTrue($test->wasRun);
    }

    public function test_exception()
    {
        $test = new ThrowExceptionTestCase('test');
        $test->setExpectedException('RuntimeException');

        $result = $test->run();

        $this->assertEquals(1, count($result));
        $this->assertTrue($result->wasSuccessful());
    }

    public function test_exception_with_message()
    {
        $test = new ThrowExceptionTestCase('test');
        $test->setExpectedException('RuntimeException', 'A runtime error occurred');

        $result = $test->run();

        $this->assertEquals(1, count($result));
        $this->assertTrue($result->wasSuccessful());
    }

    public function test_exception_with_wrong_message()
    {
        $test = new ThrowExceptionTestCase('test');
        $test->setExpectedException('RuntimeException', 'A logic error occurred');

        $result = $test->run();

        $this->assertEquals(1, $result->failureCount());
        $this->assertEquals(1, count($result));
        $this->assertEquals(
            "Failed asserting that exception message 'A runtime error occurred' contains 'A logic error occurred'.",
            $test->getStatusMessage()
        );
    }

    public function test_exception_with_regexp_message()
    {
        $test = new ThrowExceptionTestCase('test');
        $test->setExpectedExceptionRegExp('RuntimeException', '/runtime .*? occurred/');

        $result = $test->run();

        $this->assertEquals(1, count($result));
        $this->assertTrue($result->wasSuccessful());
    }

    public function test_exception_with_wrong_regexp_message()
    {
        $test = new ThrowExceptionTestCase('test');
        $test->setExpectedExceptionRegExp('RuntimeException', '/logic .*? occurred/');

        $result = $test->run();

        $this->assertEquals(1, $result->failureCount());
        $this->assertEquals(1, count($result));
        $this->assertEquals(
            "Failed asserting that exception message 'A runtime error occurred' matches '/logic .*? occurred/'.",
            $test->getStatusMessage()
        );
    }

    /**
     * @covers PHPUnit_Framework_Constraint_ExceptionMessageRegExp
     */
    public function test_exception_with_invalid_regexp_message()
    {
        $test = new ThrowExceptionTestCase('test');
        $test->setExpectedExceptionRegExp('RuntimeException', '#runtime .*? occurred/'); // wrong delimiter

        $result = $test->run();

        $this->assertEquals(
            "Invalid expected exception message regex given: '#runtime .*? occurred/'",
            $test->getStatusMessage()
        );
    }

    public function test_no_exception()
    {
        $test = new ThrowNoExceptionTestCase('test');
        $test->setExpectedException('RuntimeException');

        $result = $test->run();

        $this->assertEquals(1, $result->failureCount());
        $this->assertEquals(1, count($result));
    }

    public function test_wrong_exception()
    {
        $test = new ThrowExceptionTestCase('test');
        $test->setExpectedException('InvalidArgumentException');

        $result = $test->run();

        $this->assertEquals(1, $result->failureCount());
        $this->assertEquals(1, count($result));
    }

    /**
     * @backupGlobals enabled
     */
    public function test_globals_backup_pre()
    {
        global $a;
        global $i;

        $this->assertEquals('a', $a);
        $this->assertEquals('a', $GLOBALS['a']);
        $this->assertEquals('b', $_ENV['b']);
        $this->assertEquals('c', $_POST['c']);
        $this->assertEquals('d', $_GET['d']);
        $this->assertEquals('e', $_COOKIE['e']);
        $this->assertEquals('f', $_SERVER['f']);
        $this->assertEquals('g', $_FILES['g']);
        $this->assertEquals('h', $_REQUEST['h']);
        $this->assertEquals('i', $i);
        $this->assertEquals('i', $GLOBALS['i']);

        $GLOBALS['a'] = 'aa';
        $GLOBALS['foo'] = 'bar';
        $_ENV['b'] = 'bb';
        $_POST['c'] = 'cc';
        $_GET['d'] = 'dd';
        $_COOKIE['e'] = 'ee';
        $_SERVER['f'] = 'ff';
        $_FILES['g'] = 'gg';
        $_REQUEST['h'] = 'hh';
        $GLOBALS['i'] = 'ii';

        $this->assertEquals('aa', $a);
        $this->assertEquals('aa', $GLOBALS['a']);
        $this->assertEquals('bar', $GLOBALS['foo']);
        $this->assertEquals('bb', $_ENV['b']);
        $this->assertEquals('cc', $_POST['c']);
        $this->assertEquals('dd', $_GET['d']);
        $this->assertEquals('ee', $_COOKIE['e']);
        $this->assertEquals('ff', $_SERVER['f']);
        $this->assertEquals('gg', $_FILES['g']);
        $this->assertEquals('hh', $_REQUEST['h']);
        $this->assertEquals('ii', $i);
        $this->assertEquals('ii', $GLOBALS['i']);
    }

    public function test_globals_backup_post()
    {
        global $a;
        global $i;

        $this->assertEquals('a', $a);
        $this->assertEquals('a', $GLOBALS['a']);
        $this->assertEquals('b', $_ENV['b']);
        $this->assertEquals('c', $_POST['c']);
        $this->assertEquals('d', $_GET['d']);
        $this->assertEquals('e', $_COOKIE['e']);
        $this->assertEquals('f', $_SERVER['f']);
        $this->assertEquals('g', $_FILES['g']);
        $this->assertEquals('h', $_REQUEST['h']);
        $this->assertEquals('ii', $i);
        $this->assertEquals('ii', $GLOBALS['i']);

        $this->assertArrayNotHasKey('foo', $GLOBALS);
    }

    /**
     * @backupGlobals enabled
     *
     * @backupStaticAttributes enabled
     */
    public function test_static_attributes_backup_pre()
    {
        $GLOBALS['singleton'] = Singleton::getInstance();
        self::$_testStatic = 123;
    }

    /**
     * @depends test_static_attributes_backup_pre
     */
    public function test_static_attributes_backup_post()
    {
        $this->assertNotSame($GLOBALS['singleton'], Singleton::getInstance());
        $this->assertSame(0, self::$_testStatic);
    }

    public function test_is_in_isolation_returns_false()
    {
        $test = new IsolationTest('testIsInIsolationReturnsFalse');
        $result = $test->run();

        $this->assertEquals(1, count($result));
        $this->assertTrue($result->wasSuccessful());
    }

    public function test_is_in_isolation_returns_true()
    {
        $test = new IsolationTest('testIsInIsolationReturnsTrue');
        $test->setRunTestInSeparateProcess(true);
        $result = $test->run();

        $this->assertEquals(1, count($result));
        $this->assertTrue($result->wasSuccessful());
    }

    public function test_expect_output_string_foo_actual_foo()
    {
        $test = new OutputTestCase('testExpectOutputStringFooActualFoo');
        $result = $test->run();

        $this->assertEquals(1, count($result));
        $this->assertTrue($result->wasSuccessful());
    }

    public function test_expect_output_string_foo_actual_bar()
    {
        $test = new OutputTestCase('testExpectOutputStringFooActualBar');
        $result = $test->run();

        $this->assertEquals(1, count($result));
        $this->assertFalse($result->wasSuccessful());
    }

    public function test_expect_output_regex_foo_actual_foo()
    {
        $test = new OutputTestCase('testExpectOutputRegexFooActualFoo');
        $result = $test->run();

        $this->assertEquals(1, count($result));
        $this->assertTrue($result->wasSuccessful());
    }

    public function test_expect_output_regex_foo_actual_bar()
    {
        $test = new OutputTestCase('testExpectOutputRegexFooActualBar');
        $result = $test->run();

        $this->assertEquals(1, count($result));
        $this->assertFalse($result->wasSuccessful());
    }

    public function test_skips_if_requires_higher_version_of_php_unit()
    {
        $test = new RequirementsTest('testAlwaysSkip');
        $result = $test->run();

        $this->assertEquals(1, $result->skippedCount());
        $this->assertEquals(
            'PHPUnit 1111111 (or later) is required.',
            $test->getStatusMessage()
        );
    }

    public function test_skips_if_requires_higher_version_of_php()
    {
        $test = new RequirementsTest('testAlwaysSkip2');
        $result = $test->run();

        $this->assertEquals(1, $result->skippedCount());
        $this->assertEquals(
            'PHP 9999999 (or later) is required.',
            $test->getStatusMessage()
        );
    }

    public function test_skips_if_requires_non_existing_os()
    {
        $test = new RequirementsTest('testAlwaysSkip3');
        $result = $test->run();

        $this->assertEquals(1, $result->skippedCount());
        $this->assertEquals(
            'Operating system matching /DOESNOTEXIST/i is required.',
            $test->getStatusMessage()
        );
    }

    public function test_skips_if_requires_non_existing_function()
    {
        $test = new RequirementsTest('testNine');
        $result = $test->run();

        $this->assertEquals(1, $result->skippedCount());
        $this->assertEquals(
            'Function testFunc is required.',
            $test->getStatusMessage()
        );
    }

    public function test_skips_if_requires_non_existing_extension()
    {
        $test = new RequirementsTest('testTen');
        $result = $test->run();

        $this->assertEquals(
            'Extension testExt is required.',
            $test->getStatusMessage()
        );
    }

    public function test_skips_provides_messages_for_all_skipping_reasons()
    {
        $test = new RequirementsTest('testAllPossibleRequirements');
        $result = $test->run();

        $this->assertEquals(
            'PHP 99-dev (or later) is required.'.PHP_EOL.
            'PHPUnit 9-dev (or later) is required.'.PHP_EOL.
            'Operating system matching /DOESNOTEXIST/i is required.'.PHP_EOL.
            'Function testFuncOne is required.'.PHP_EOL.
            'Function testFuncTwo is required.'.PHP_EOL.
            'Extension testExtOne is required.'.PHP_EOL.
            'Extension testExtTwo is required.',
            $test->getStatusMessage()
        );
    }

    public function test_requiring_an_existing_method_does_not_skip()
    {
        $test = new RequirementsTest('testExistingMethod');
        $result = $test->run();
        $this->assertEquals(0, $result->skippedCount());
    }

    public function test_requiring_an_existing_function_does_not_skip()
    {
        $test = new RequirementsTest('testExistingFunction');
        $result = $test->run();
        $this->assertEquals(0, $result->skippedCount());
    }

    public function test_requiring_an_existing_extension_does_not_skip()
    {
        $test = new RequirementsTest('testExistingExtension');
        $result = $test->run();
        $this->assertEquals(0, $result->skippedCount());
    }

    public function test_requiring_an_existing_os_does_not_skip()
    {
        $test = new RequirementsTest('testExistingOs');
        $result = $test->run();
        $this->assertEquals(0, $result->skippedCount());
    }

    public function test_current_working_directory_is_restored()
    {
        $expectedCwd = getcwd();

        $test = new ChangeCurrentWorkingDirectoryTest('testSomethingThatChangesTheCwd');
        $test->run();

        $this->assertSame($expectedCwd, getcwd());
    }

    /**
     * @requires PHP 7
     *
     * @expectedException TypeError
     */
    public function test_type_error_can_be_expected()
    {
        $o = new ClassWithScalarTypeDeclarations;
        $o->foo(null, null);
    }
}
