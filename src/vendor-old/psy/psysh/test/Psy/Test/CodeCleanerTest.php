<?php

/*
 * This file is part of Psy Shell.
 *
 * (c) 2012-2015 Justin Hileman
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Psy\Test;

use Psy\CodeCleaner;

class CodeCleanerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @dataProvider semicolonCodeProvider
     */
    public function test_automatic_semicolons(array $lines, $requireSemicolons, $expected)
    {
        $cc = new CodeCleaner;
        $this->assertEquals($expected, $cc->clean($lines, $requireSemicolons));
    }

    public function semicolonCodeProvider()
    {
        return [
            [['true'],  false, 'return true;'],
            [['true;'], false, 'return true;'],
            [['true;'], true,  'return true;'],
            [['true'],  true,  false],

            [['echo "foo";', 'true'], false, "echo 'foo';\nreturn true;"],
            [['echo "foo";', 'true'], true,  false],
        ];
    }

    /**
     * @dataProvider unclosedStatementsProvider
     */
    public function test_unclosed_statements(array $lines, $isUnclosed)
    {
        $cc = new CodeCleaner;
        $res = $cc->clean($lines);

        if ($isUnclosed) {
            $this->assertFalse($res);
        } else {
            $this->assertNotFalse($res);
        }
    }

    public function unclosedStatementsProvider()
    {
        return [
            [['echo "'],   true],
            [['echo \''],  true],
            [['if (1) {'], true],

            [['echo ""'],   false],
            [["echo ''"],   false],
            [['if (1) {}'], false],

            [["\$content = <<<EOS\n"],   true],
            [["\$content = <<<'EOS'\n"], true],
        ];
    }

    /**
     * @dataProvider invalidStatementsProvider
     *
     * @expectedException Psy\Exception\ParseErrorException
     */
    public function test_invalid_statements_throw_parse_errors($code)
    {
        $cc = new CodeCleaner;
        $cc->clean([$code]);
    }

    public function invalidStatementsProvider()
    {
        return [
            ['function "what'],
            ["function 'what"],
            ['echo }'],
            ['echo {'],
            ['if (1) }'],
            ['echo """'],
            ["echo '''"],
            ['$foo "bar'],
            ['$foo \'bar'],
        ];
    }
}
