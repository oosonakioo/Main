<?php

/*
 * This file is part of Psy Shell.
 *
 * (c) 2012-2015 Justin Hileman
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Psy\Test\CodeCleaner;

use Psy\CodeCleaner\ExitPass;

class ExitPassTest extends CodeCleanerTestCase
{
    /**
     * @var string
     */
    private $expectedExceptionString = "throw new Psy\\Exception\\BreakException('Goodbye.');";

    protected function setUp()
    {
        $this->setPass(new ExitPass);
    }

    /**
     * @dataProvider dataProviderExitStatement
     */
    public function test_exit_statement($from, $to)
    {
        $this->assertProcessesAs($from, $to);
    }

    /**
     * Data provider for testExitStatement.
     *
     * @return array
     */
    public function dataProviderExitStatement()
    {
        return [
            ['exit;', $this->expectedExceptionString],
            ['exit();', $this->expectedExceptionString],
            ['die;', $this->expectedExceptionString],
            ['if (true) { exit; }', "if (true) {\n    $this->expectedExceptionString\n}"],
            ['if (false) { exit; }', "if (false) {\n    $this->expectedExceptionString\n}"],
        ];
    }
}
