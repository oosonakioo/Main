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

use Psy\CodeCleaner\LeavePsyshAlonePass;

class LeavePsyshAlonePassTest extends CodeCleanerTestCase
{
    protected function setUp()
    {
        $this->setPass(new LeavePsyshAlonePass);
    }

    public function test_passes_inline_html_through_just_fine()
    {
        $inline = $this->parse('not php at all!', '');
        $this->traverse($inline);
    }

    /**
     * @dataProvider validStatements
     */
    public function test_process_statement_passes($code)
    {
        $stmts = $this->parse($code);
        $this->traverse($stmts);
    }

    public function validStatements()
    {
        return [
            ['array_merge()'],
            ['__psysh__()'],
            ['$this'],
            ['$psysh'],
            ['$__psysh'],
            ['$banana'],
        ];
    }

    /**
     * @dataProvider invalidStatements
     *
     * @expectedException \Psy\Exception\RuntimeException
     */
    public function test_process_statement_fails($code)
    {
        $stmts = $this->parse($code);
        $this->traverse($stmts);
    }

    public function invalidStatements()
    {
        return [
            ['$__psysh__'],
            ['var_dump($__psysh__)'],
            ['$__psysh__ = "your mom"'],
            ['$__psysh__->fakeFunctionCall()'],
        ];
    }
}
