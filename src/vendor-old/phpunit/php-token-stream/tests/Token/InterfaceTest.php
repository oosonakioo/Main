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
 * Tests for the PHP_Token_INTERFACE class.
 *
 * @author     Sebastian Bergmann <sebastian@phpunit.de>
 * @author     Laurent Laville <pear@laurent-laville.org>
 * @copyright  Sebastian Bergmann <sebastian@phpunit.de>
 * @license    http://www.opensource.org/licenses/BSD-3-Clause  The BSD 3-Clause License
 *
 * @version    Release: @package_version@
 *
 * @link       http://github.com/sebastianbergmann/php-token-stream/
 * @since      Class available since Release 1.0.0
 */
class PHP_Token_InterfaceTest extends PHPUnit_Framework_TestCase
{
    protected $class;

    protected $interfaces;

    protected function setUp()
    {
        $ts = new PHP_Token_Stream(TEST_FILES_PATH.'source4.php');
        $i = 0;
        foreach ($ts as $token) {
            if ($token instanceof PHP_Token_CLASS) {
                $this->class = $token;
            } elseif ($token instanceof PHP_Token_INTERFACE) {
                $this->interfaces[$i] = $token;
                $i++;
            }
        }
    }

    /**
     * @covers PHP_Token_INTERFACE::getName
     */
    public function test_get_name()
    {
        $this->assertEquals(
            'iTemplate', $this->interfaces[0]->getName()
        );
    }

    /**
     * @covers PHP_Token_INTERFACE::getParent
     */
    public function test_get_parent_not_exists()
    {
        $this->assertFalse(
            $this->interfaces[0]->getParent()
        );
    }

    /**
     * @covers PHP_Token_INTERFACE::hasParent
     */
    public function test_has_parent_not_exists()
    {
        $this->assertFalse(
            $this->interfaces[0]->hasParent()
        );
    }

    /**
     * @covers PHP_Token_INTERFACE::getParent
     */
    public function test_get_parent_exists()
    {
        $this->assertEquals(
            'a', $this->interfaces[2]->getParent()
        );
    }

    /**
     * @covers PHP_Token_INTERFACE::hasParent
     */
    public function test_has_parent_exists()
    {
        $this->assertTrue(
            $this->interfaces[2]->hasParent()
        );
    }

    /**
     * @covers PHP_Token_INTERFACE::getInterfaces
     */
    public function test_get_interfaces_exists()
    {
        $this->assertEquals(
            ['b'],
            $this->class->getInterfaces()
        );
    }

    /**
     * @covers PHP_Token_INTERFACE::hasInterfaces
     */
    public function test_has_interfaces_exists()
    {
        $this->assertTrue(
            $this->class->hasInterfaces()
        );
    }

    /**
     * @covers PHP_Token_INTERFACE::getPackage
     */
    public function test_get_package_namespace()
    {
        $tokenStream = new PHP_Token_Stream(TEST_FILES_PATH.'classInNamespace.php');
        foreach ($tokenStream as $token) {
            if ($token instanceof PHP_Token_INTERFACE) {
                $package = $token->getPackage();
                $this->assertSame('Foo\\Bar', $package['namespace']);
            }
        }
    }

    public function provideFilesWithClassesWithinMultipleNamespaces()
    {
        return [
            [TEST_FILES_PATH.'multipleNamespacesWithOneClassUsingBraces.php'],
            [TEST_FILES_PATH.'multipleNamespacesWithOneClassUsingNonBraceSyntax.php'],
        ];
    }

    /**
     * @dataProvider provideFilesWithClassesWithinMultipleNamespaces
     *
     * @covers PHP_Token_INTERFACE::getPackage
     */
    public function test_get_package_namespace_for_file_with_multiple_namespaces($filepath)
    {
        $tokenStream = new PHP_Token_Stream($filepath);
        $firstClassFound = false;
        foreach ($tokenStream as $token) {
            if ($firstClassFound === false && $token instanceof PHP_Token_INTERFACE) {
                $package = $token->getPackage();
                $this->assertSame('TestClassInBar', $token->getName());
                $this->assertSame('Foo\\Bar', $package['namespace']);
                $firstClassFound = true;

                continue;
            }
            // Secound class
            if ($token instanceof PHP_Token_INTERFACE) {
                $package = $token->getPackage();
                $this->assertSame('TestClassInBaz', $token->getName());
                $this->assertSame('Foo\\Baz', $package['namespace']);

                return;
            }
        }
        $this->fail('Seachring for 2 classes failed');
    }

    public function test_get_package_namespace_is_empty_for_interfaces_that_are_not_within_namespaces()
    {
        foreach ($this->interfaces as $token) {
            $package = $token->getPackage();
            $this->assertSame('', $package['namespace']);
        }
    }

    /**
     * @covers PHP_Token_INTERFACE::getPackage
     */
    public function test_get_package_namespace_when_extenting_from_namespace_class()
    {
        $tokenStream = new PHP_Token_Stream(TEST_FILES_PATH.'classExtendsNamespacedClass.php');
        $firstClassFound = false;
        foreach ($tokenStream as $token) {
            if ($firstClassFound === false && $token instanceof PHP_Token_INTERFACE) {
                $package = $token->getPackage();
                $this->assertSame('Baz', $token->getName());
                $this->assertSame('Foo\\Bar', $package['namespace']);
                $firstClassFound = true;

                continue;
            }
            if ($token instanceof PHP_Token_INTERFACE) {
                $package = $token->getPackage();
                $this->assertSame('Extender', $token->getName());
                $this->assertSame('Other\\Space', $package['namespace']);

                return;
            }
        }
        $this->fail('Searching for 2 classes failed');
    }
}
