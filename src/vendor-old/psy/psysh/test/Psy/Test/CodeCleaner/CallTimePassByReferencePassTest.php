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

use PhpParser\NodeTraverser;
use Psy\CodeCleaner\CallTimePassByReferencePass;

class CallTimePassByReferencePassTest extends CodeCleanerTestCase
{
    protected function setUp()
    {
        $this->pass = new CallTimePassByReferencePass;
        $this->traverser = new NodeTraverser;
        $this->traverser->addVisitor($this->pass);
    }

    /**
     * @dataProvider invalidStatements
     *
     * @expectedException \Psy\Exception\FatalErrorException
     */
    public function test_process_statement_fails($code)
    {
        if (version_compare(PHP_VERSION, '5.4', '<')) {
            $this->markTestSkipped();
        }

        $stmts = $this->parse($code);
        $this->traverser->traverse($stmts);
    }

    public function invalidStatements()
    {
        return [
            ['f(&$arg)'],
            ['$object->method($first, &$arg)'],
            ['$closure($first, &$arg, $last)'],
            ['A::b(&$arg)'],
        ];
    }

    /**
     * @dataProvider validStatements
     */
    public function test_process_statement_passes($code)
    {
        $stmts = $this->parse($code);
        $this->traverser->traverse($stmts);
    }

    public function validStatements()
    {
        $data = [
            ['array(&$var)'],
            ['$a = &$b'],
            ['f(array(&$b))'],
        ];

        if (version_compare(PHP_VERSION, '5.4', '<')) {
            $data = array_merge($data, $this->invalidStatements());
        }

        return $data;
    }
}
