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

use Psy\CodeCleaner\ValidFunctionNamePass;

class ValidFunctionNamePassTest extends CodeCleanerTestCase
{
    protected function setUp()
    {
        $this->setPass(new ValidFunctionNamePass);
    }

    /**
     * @dataProvider getInvalidFunctions
     *
     * @expectedException \Psy\Exception\FatalErrorException
     */
    public function test_process_invalid_function_calls_and_declarations($code)
    {
        $stmts = $this->parse($code);
        $this->traverse($stmts);
    }

    public function getInvalidFunctions()
    {
        return [
            // function declarations
            ['function array_merge() {}'],
            ['function Array_Merge() {}'],
            ['
                function psy_test_codecleaner_validfunctionnamepass_alpha() {}
                function psy_test_codecleaner_validfunctionnamepass_alpha() {}
            '],
            ['
                namespace Psy\\Test\\CodeCleaner\\ValidFunctionNamePass {
                    function beta() {}
                }
                namespace Psy\\Test\\CodeCleaner\\ValidFunctionNamePass {
                    function beta() {}
                }
            '],

            // function calls
            ['psy_test_codecleaner_validfunctionnamepass_gamma()'],
            ['
                namespace Psy\\Test\\CodeCleaner\\ValidFunctionNamePass {
                    delta();
                }
            '],

            // recursion
            ['function a() { a(); } function a() {}'],
        ];
    }

    /**
     * @dataProvider getValidFunctions
     */
    public function test_process_valid_function_calls_and_declarations($code)
    {
        $stmts = $this->parse($code);
        $this->traverse($stmts);
    }

    public function getValidFunctions()
    {
        return [
            ['function psy_test_codecleaner_validfunctionnamepass_epsilon() {}'],
            ['
                namespace Psy\\Test\\CodeCleaner\\ValidFunctionNamePass {
                    function zeta() {}
                }
            '],
            ['
                namespace {
                    function psy_test_codecleaner_validfunctionnamepass_eta() {}
                }
                namespace Psy\\Test\\CodeCleaner\\ValidFunctionNamePass {
                    function psy_test_codecleaner_validfunctionnamepass_eta() {}
                }
            '],
            ['
                namespace Psy\\Test\\CodeCleaner\\ValidFunctionNamePass {
                    function psy_test_codecleaner_validfunctionnamepass_eta() {}
                }
                namespace {
                    function psy_test_codecleaner_validfunctionnamepass_eta() {}
                }
            '],
            ['
                namespace Psy\\Test\\CodeCleaner\\ValidFunctionNamePass {
                    function array_merge() {}
                }
            '],

            // function calls
            ['array_merge();'],
            ['
                namespace Psy\\Test\\CodeCleaner\\ValidFunctionNamePass {
                    function theta() {}
                }
                namespace Psy\\Test\\CodeCleaner\\ValidFunctionNamePass {
                    theta();
                }
            '],
            // closures
            ['$test = function(){};$test()'],
            ['
                namespace Psy\\Test\\CodeCleaner\\ValidFunctionNamePass {
                    function theta() {}
                }
                namespace {
                    Psy\\Test\\CodeCleaner\\ValidFunctionNamePass\\theta();
                }
            '],

            // recursion
            ['function a() { a(); }'],
        ];
    }
}
