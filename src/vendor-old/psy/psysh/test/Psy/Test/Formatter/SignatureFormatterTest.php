<?php

/*
 * This file is part of Psy Shell.
 *
 * (c) 2012-2015 Justin Hileman
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Psy\Test\Formatter;

use Psy\Formatter\SignatureFormatter;
use Psy\Reflection\ReflectionConstant;

class SignatureFormatterTest extends \PHPUnit_Framework_TestCase
{
    const FOO = 'foo value';

    private static $bar = 'bar value';

    private function someFakeMethod(array $one, $two = 'TWO', ?\Reflector $three = null) {}

    /**
     * @dataProvider signatureReflectors
     */
    public function test_format($reflector, $expected)
    {
        $this->assertEquals($expected, strip_tags(SignatureFormatter::format($reflector)));
    }

    public function signatureReflectors()
    {
        return [
            [
                new \ReflectionClass($this),
                "class Psy\Test\Formatter\SignatureFormatterTest "
                .'extends PHPUnit_Framework_TestCase implements '
                .'Countable, PHPUnit_Framework_SelfDescribing, '
                .'PHPUnit_Framework_Test',
            ],
            [
                new \ReflectionFunction('implode'),
                defined('HHVM_VERSION') ? 'function implode($arg1, $arg2 = null)' : 'function implode($glue, $pieces)',
            ],
            [
                new ReflectionConstant($this, 'FOO'),
                'const FOO = "foo value"',
            ],
            [
                new \ReflectionMethod($this, 'someFakeMethod'),
                'private function someFakeMethod(array $one, $two = \'TWO\', Reflector $three = null)',
            ],
            [
                new \ReflectionProperty($this, 'bar'),
                'private static $bar',
            ],
            [
                new \ReflectionClass('Psy\CodeCleaner\CodeCleanerPass'),
                'abstract class Psy\CodeCleaner\CodeCleanerPass '
                .'extends PhpParser\NodeVisitorAbstract '
                .'implements PhpParser\NodeVisitor',
            ],
        ];
    }

    /**
     * @expectedException InvalidArgumentException
     */
    public function test_signature_formatter_throws_unknown_reflector_expeption()
    {
        $refl = $this->getMock('Reflector');
        SignatureFormatter::format($refl);
    }
}
