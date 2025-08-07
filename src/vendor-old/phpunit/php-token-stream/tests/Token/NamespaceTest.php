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
 * Tests for the PHP_Token_NAMESPACE class.
 *
 * @author     Sebastian Bergmann <sebastian@phpunit.de>
 * @copyright  Sebastian Bergmann <sebastian@phpunit.de>
 * @license    http://www.opensource.org/licenses/BSD-3-Clause  The BSD 3-Clause License
 *
 * @version    Release: @package_version@
 *
 * @link       http://github.com/sebastianbergmann/php-token-stream/
 * @since      Class available since Release 1.0.0
 */
class PHP_Token_NamespaceTest extends PHPUnit_Framework_TestCase
{
    /**
     * @covers PHP_Token_NAMESPACE::getName
     */
    public function test_get_name()
    {
        $tokenStream = new PHP_Token_Stream(
            TEST_FILES_PATH.'classInNamespace.php'
        );

        foreach ($tokenStream as $token) {
            if ($token instanceof PHP_Token_NAMESPACE) {
                $this->assertSame('Foo\\Bar', $token->getName());
            }
        }
    }

    public function test_get_start_line_with_unscoped_namespace()
    {
        $tokenStream = new PHP_Token_Stream(TEST_FILES_PATH.'classInNamespace.php');
        foreach ($tokenStream as $token) {
            if ($token instanceof PHP_Token_NAMESPACE) {
                $this->assertSame(2, $token->getLine());
            }
        }
    }

    public function test_get_end_line_with_unscoped_namespace()
    {
        $tokenStream = new PHP_Token_Stream(TEST_FILES_PATH.'classInNamespace.php');
        foreach ($tokenStream as $token) {
            if ($token instanceof PHP_Token_NAMESPACE) {
                $this->assertSame(2, $token->getEndLine());
            }
        }
    }

    public function test_get_start_line_with_scoped_namespace()
    {
        $tokenStream = new PHP_Token_Stream(TEST_FILES_PATH.'classInScopedNamespace.php');
        foreach ($tokenStream as $token) {
            if ($token instanceof PHP_Token_NAMESPACE) {
                $this->assertSame(2, $token->getLine());
            }
        }
    }

    public function test_get_end_line_with_scoped_namespace()
    {
        $tokenStream = new PHP_Token_Stream(TEST_FILES_PATH.'classInScopedNamespace.php');
        foreach ($tokenStream as $token) {
            if ($token instanceof PHP_Token_NAMESPACE) {
                $this->assertSame(8, $token->getEndLine());
            }
        }
    }
}
