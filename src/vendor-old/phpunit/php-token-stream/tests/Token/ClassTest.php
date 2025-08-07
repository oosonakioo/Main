<?php

/*
 * This file is part of the PHP_TokenStream package.
 *
 * (c) Sebastian Bergmann <sebastian@phpunit.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * Tests for the PHP_Token_CLASS class.
 *
 * @author     Laurent Laville <pear@laurent-laville.org>
 * @copyright  Sebastian Bergmann <sebastian@phpunit.de>
 * @license    http://www.opensource.org/licenses/BSD-3-Clause  The BSD 3-Clause License
 *
 * @version    Release: @package_version@
 *
 * @link       http://github.com/sebastianbergmann/php-token-stream/
 * @since      Class available since Release 1.0.2
 */
class PHP_Token_ClassTest extends PHPUnit_Framework_TestCase
{
    protected $class;

    protected $function;

    protected function setUp()
    {
        $ts = new PHP_Token_Stream(TEST_FILES_PATH.'source2.php');

        foreach ($ts as $token) {
            if ($token instanceof PHP_Token_CLASS) {
                $this->class = $token;
            }

            if ($token instanceof PHP_Token_FUNCTION) {
                $this->function = $token;
                break;
            }
        }
    }

    /**
     * @covers PHP_Token_CLASS::getKeywords
     */
    public function test_get_class_keywords()
    {
        $this->assertEquals('abstract', $this->class->getKeywords());
    }

    /**
     * @covers PHP_Token_FUNCTION::getKeywords
     */
    public function test_get_function_keywords()
    {
        $this->assertEquals('abstract,static', $this->function->getKeywords());
    }

    /**
     * @covers PHP_Token_FUNCTION::getVisibility
     */
    public function test_get_function_visibility()
    {
        $this->assertEquals('public', $this->function->getVisibility());
    }

    public function test_issue19()
    {
        $ts = new PHP_Token_Stream(TEST_FILES_PATH.'issue19.php');

        foreach ($ts as $token) {
            if ($token instanceof PHP_Token_CLASS) {
                $this->assertFalse($token->hasInterfaces());
            }
        }
    }

    public function test_issue30()
    {
        $ts = new PHP_Token_Stream(TEST_FILES_PATH.'issue30.php');
        $this->assertCount(1, $ts->getClasses());
    }

    /**
     * @requires PHP 7
     */
    public function test_anonymous_classes_are_handled_correctly()
    {
        $ts = new PHP_Token_Stream(TEST_FILES_PATH.'class_with_method_that_declares_anonymous_class.php');

        $classes = $ts->getClasses();

        $this->assertEquals(['class_with_method_that_declares_anonymous_class'], array_keys($classes));
    }

    /**
     * @requires PHP 7
     *
     * @ticket   https://github.com/sebastianbergmann/php-token-stream/issues/52
     */
    public function test_anonymous_classes_are_handled_correctly2()
    {
        $ts = new PHP_Token_Stream(TEST_FILES_PATH.'class_with_method_that_declares_anonymous_class2.php');

        $classes = $ts->getClasses();

        $this->assertEquals(['Test'], array_keys($classes));
        $this->assertEquals(['methodOne', 'methodTwo'], array_keys($classes['Test']['methods']));

        $this->assertEmpty($ts->getFunctions());
    }

    /**
     * @requires PHP 5.6
     */
    public function test_imported_functions_are_handled_correctly()
    {
        $ts = new PHP_Token_Stream(TEST_FILES_PATH.'classUsesNamespacedFunction.php');

        $this->assertEmpty($ts->getFunctions());
        $this->assertCount(1, $ts->getClasses());
    }
}
