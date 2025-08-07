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
 * Tests for the PHP_Token_FUNCTION class.
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
class PHP_Token_FunctionTest extends PHPUnit_Framework_TestCase
{
    protected $functions;

    protected function setUp()
    {
        $ts = new PHP_Token_Stream(TEST_FILES_PATH.'source.php');

        foreach ($ts as $token) {
            if ($token instanceof PHP_Token_FUNCTION) {
                $this->functions[] = $token;
            }
        }
    }

    /**
     * @covers PHP_Token_FUNCTION::getArguments
     */
    public function test_get_arguments()
    {
        $this->assertEquals([], $this->functions[0]->getArguments());

        $this->assertEquals(
            ['$baz' => 'Baz'], $this->functions[1]->getArguments()
        );

        $this->assertEquals(
            ['$foobar' => 'Foobar'], $this->functions[2]->getArguments()
        );

        $this->assertEquals(
            ['$barfoo' => 'Barfoo'], $this->functions[3]->getArguments()
        );

        $this->assertEquals([], $this->functions[4]->getArguments());

        $this->assertEquals(['$x' => null, '$y' => null], $this->functions[5]->getArguments());
    }

    /**
     * @covers PHP_Token_FUNCTION::getName
     */
    public function test_get_name()
    {
        $this->assertEquals('foo', $this->functions[0]->getName());
        $this->assertEquals('bar', $this->functions[1]->getName());
        $this->assertEquals('foobar', $this->functions[2]->getName());
        $this->assertEquals('barfoo', $this->functions[3]->getName());
        $this->assertEquals('baz', $this->functions[4]->getName());
    }

    /**
     * @covers PHP_Token::getLine
     */
    public function test_get_line()
    {
        $this->assertEquals(5, $this->functions[0]->getLine());
        $this->assertEquals(10, $this->functions[1]->getLine());
        $this->assertEquals(17, $this->functions[2]->getLine());
        $this->assertEquals(21, $this->functions[3]->getLine());
        $this->assertEquals(29, $this->functions[4]->getLine());
    }

    /**
     * @covers PHP_TokenWithScope::getEndLine
     */
    public function test_get_end_line()
    {
        $this->assertEquals(5, $this->functions[0]->getEndLine());
        $this->assertEquals(12, $this->functions[1]->getEndLine());
        $this->assertEquals(19, $this->functions[2]->getEndLine());
        $this->assertEquals(23, $this->functions[3]->getEndLine());
        $this->assertEquals(31, $this->functions[4]->getEndLine());
    }

    /**
     * @covers PHP_Token_FUNCTION::getDocblock
     */
    public function test_get_docblock()
    {
        $this->assertNull($this->functions[0]->getDocblock());

        $this->assertEquals(
            "/**\n     * @param Baz \$baz\n     */",
            $this->functions[1]->getDocblock()
        );

        $this->assertEquals(
            "/**\n     * @param Foobar \$foobar\n     */",
            $this->functions[2]->getDocblock()
        );

        $this->assertNull($this->functions[3]->getDocblock());
        $this->assertNull($this->functions[4]->getDocblock());
    }

    public function test_signature()
    {
        $ts = new PHP_Token_Stream(TEST_FILES_PATH.'source5.php');
        $f = $ts->getFunctions();
        $c = $ts->getClasses();
        $i = $ts->getInterfaces();

        $this->assertEquals(
            'foo($a, array $b, array $c = array())',
            $f['foo']['signature']
        );

        $this->assertEquals(
            'm($a, array $b, array $c = array())',
            $c['c']['methods']['m']['signature']
        );

        $this->assertEquals(
            'm($a, array $b, array $c = array())',
            $c['a']['methods']['m']['signature']
        );

        $this->assertEquals(
            'm($a, array $b, array $c = array())',
            $i['i']['methods']['m']['signature']
        );
    }
}
