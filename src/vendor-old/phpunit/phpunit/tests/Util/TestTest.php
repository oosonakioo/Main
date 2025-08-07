<?php

/*
 * This file is part of PHPUnit.
 *
 * (c) Sebastian Bergmann <sebastian@phpunit.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

if (! defined('TEST_FILES_PATH')) {
    define(
        'TEST_FILES_PATH',
        dirname(__DIR__).DIRECTORY_SEPARATOR.
        '_files'.DIRECTORY_SEPARATOR
    );
}

require TEST_FILES_PATH.'CoverageNamespacedFunctionTest.php';
require TEST_FILES_PATH.'NamespaceCoveredFunction.php';

/**
 * @since      Class available since Release 3.3.6
 */
class Util_TestTest extends PHPUnit_Framework_TestCase
{
    /**
     * @covers PHPUnit_Util_Test::getExpectedException
     *
     * @todo   Split up in separate tests
     */
    public function test_get_expected_exception()
    {
        $this->assertArraySubset(
            ['class' => 'FooBarBaz', 'code' => null, 'message' => ''],
            PHPUnit_Util_Test::getExpectedException('ExceptionTest', 'testOne')
        );

        $this->assertArraySubset(
            ['class' => 'Foo_Bar_Baz', 'code' => null, 'message' => ''],
            PHPUnit_Util_Test::getExpectedException('ExceptionTest', 'testTwo')
        );

        $this->assertArraySubset(
            ['class' => 'Foo\Bar\Baz', 'code' => null, 'message' => ''],
            PHPUnit_Util_Test::getExpectedException('ExceptionTest', 'testThree')
        );

        $this->assertArraySubset(
            ['class' => 'ほげ', 'code' => null, 'message' => ''],
            PHPUnit_Util_Test::getExpectedException('ExceptionTest', 'testFour')
        );

        $this->assertArraySubset(
            ['class' => 'Class', 'code' => 1234, 'message' => 'Message'],
            PHPUnit_Util_Test::getExpectedException('ExceptionTest', 'testFive')
        );

        $this->assertArraySubset(
            ['class' => 'Class', 'code' => 1234, 'message' => 'Message'],
            PHPUnit_Util_Test::getExpectedException('ExceptionTest', 'testSix')
        );

        $this->assertArraySubset(
            ['class' => 'Class', 'code' => 'ExceptionCode', 'message' => 'Message'],
            PHPUnit_Util_Test::getExpectedException('ExceptionTest', 'testSeven')
        );

        $this->assertArraySubset(
            ['class' => 'Class', 'code' => 0, 'message' => 'Message'],
            PHPUnit_Util_Test::getExpectedException('ExceptionTest', 'testEight')
        );

        $this->assertArraySubset(
            ['class' => 'Class', 'code' => ExceptionTest::ERROR_CODE, 'message' => ExceptionTest::ERROR_MESSAGE],
            PHPUnit_Util_Test::getExpectedException('ExceptionTest', 'testNine')
        );

        $this->assertArraySubset(
            ['class' => 'Class', 'code' => null, 'message' => ''],
            PHPUnit_Util_Test::getExpectedException('ExceptionTest', 'testSingleLine')
        );

        $this->assertArraySubset(
            ['class' => 'Class', 'code' => My\Space\ExceptionNamespaceTest::ERROR_CODE, 'message' => My\Space\ExceptionNamespaceTest::ERROR_MESSAGE],
            PHPUnit_Util_Test::getExpectedException('My\Space\ExceptionNamespaceTest', 'testConstants')
        );

        // Ensure the Class::CONST expression is only evaluated when the constant really exists
        $this->assertArraySubset(
            ['class' => 'Class', 'code' => 'ExceptionTest::UNKNOWN_CODE_CONSTANT', 'message' => 'ExceptionTest::UNKNOWN_MESSAGE_CONSTANT'],
            PHPUnit_Util_Test::getExpectedException('ExceptionTest', 'testUnknownConstants')
        );

        $this->assertArraySubset(
            ['class' => 'Class', 'code' => 'My\Space\ExceptionNamespaceTest::UNKNOWN_CODE_CONSTANT', 'message' => 'My\Space\ExceptionNamespaceTest::UNKNOWN_MESSAGE_CONSTANT'],
            PHPUnit_Util_Test::getExpectedException('My\Space\ExceptionNamespaceTest', 'testUnknownConstants')
        );
    }

    /**
     * @covers PHPUnit_Util_Test::getExpectedException
     */
    public function test_get_expected_reg_exp()
    {
        $this->assertArraySubset(
            ['message_regex' => '#regex#'],
            PHPUnit_Util_Test::getExpectedException('ExceptionTest', 'testWithRegexMessage')
        );

        $this->assertArraySubset(
            ['message_regex' => '#regex#'],
            PHPUnit_Util_Test::getExpectedException('ExceptionTest', 'testWithRegexMessageFromClassConstant')
        );

        $this->assertArraySubset(
            ['message_regex' => 'ExceptionTest::UNKNOWN_MESSAGE_REGEX_CONSTANT'],
            PHPUnit_Util_Test::getExpectedException('ExceptionTest', 'testWithUnknowRegexMessageFromClassConstant')
        );
    }

    /**
     * @covers       PHPUnit_Util_Test::getRequirements
     *
     * @dataProvider requirementsProvider
     */
    public function test_get_requirements($test, $result)
    {
        $this->assertEquals(
            $result,
            PHPUnit_Util_Test::getRequirements('RequirementsTest', $test)
        );
    }

    public function requirementsProvider()
    {
        return [
            ['testOne',    []],
            ['testTwo',    ['PHPUnit' => '1.0']],
            ['testThree',  ['PHP' => '2.0']],
            ['testFour',   ['PHPUnit' => '2.0', 'PHP' => '1.0']],
            ['testFive',   ['PHP' => '5.4.0RC6']],
            ['testSix',    ['PHP' => '5.4.0-alpha1']],
            ['testSeven',  ['PHP' => '5.4.0beta2']],
            ['testEight',  ['PHP' => '5.4-dev']],
            ['testNine',   ['functions' => ['testFunc']]],
            ['testTen',    ['extensions' => ['testExt']]],
            ['testEleven', ['OS' => '/Linux/i']],
            [
                'testSpace',
                [
                    'extensions' => ['spl'],
                    'OS' => '/.*/i',
                ],
            ],
            [
                'testAllPossibleRequirements',
                [
                    'PHP' => '99-dev',
                    'PHPUnit' => '9-dev',
                    'OS' => '/DOESNOTEXIST/i',
                    'functions' => [
                        'testFuncOne',
                        'testFuncTwo',
                    ],
                    'extensions' => [
                        'testExtOne',
                        'testExtTwo',
                    ],
                ],
            ],
        ];
    }

    /**
     * @covers PHPUnit_Util_Test::getRequirements
     */
    public function test_get_requirements_merges_class_and_method_doc_blocks()
    {
        $expectedAnnotations = [
            'PHP' => '5.4',
            'PHPUnit' => '3.7',
            'OS' => '/WINNT/i',
            'functions' => [
                'testFuncClass',
                'testFuncMethod',
            ],
            'extensions' => [
                'testExtClass',
                'testExtMethod',
            ],
        ];

        $this->assertEquals(
            $expectedAnnotations,
            PHPUnit_Util_Test::getRequirements('RequirementsClassDocBlockTest', 'testMethod')
        );
    }

    /**
     * @covers       PHPUnit_Util_Test::getMissingRequirements
     *
     * @dataProvider missingRequirementsProvider
     */
    public function test_get_missing_requirements($test, $result)
    {
        $this->assertEquals(
            $result,
            PHPUnit_Util_Test::getMissingRequirements('RequirementsTest', $test)
        );
    }

    public function missingRequirementsProvider()
    {
        return [
            ['testOne',            []],
            ['testNine',           ['Function testFunc is required.']],
            ['testTen',            ['Extension testExt is required.']],
            ['testAlwaysSkip',     ['PHPUnit 1111111 (or later) is required.']],
            ['testAlwaysSkip2',    ['PHP 9999999 (or later) is required.']],
            ['testAlwaysSkip3',    ['Operating system matching /DOESNOTEXIST/i is required.']],
            ['testAllPossibleRequirements', [
                'PHP 99-dev (or later) is required.',
                'PHPUnit 9-dev (or later) is required.',
                'Operating system matching /DOESNOTEXIST/i is required.',
                'Function testFuncOne is required.',
                'Function testFuncTwo is required.',
                'Extension testExtOne is required.',
                'Extension testExtTwo is required.',
            ]],
        ];
    }

    /**
     * @coversNothing
     *
     * @todo   This test does not really test functionality of PHPUnit_Util_Test
     */
    public function test_get_provided_data_reg_ex()
    {
        $result = preg_match(PHPUnit_Util_Test::REGEX_DATA_PROVIDER, '@dataProvider method', $matches);
        $this->assertEquals(1, $result);
        $this->assertEquals('method', $matches[1]);

        $result = preg_match(PHPUnit_Util_Test::REGEX_DATA_PROVIDER, '@dataProvider class::method', $matches);
        $this->assertEquals(1, $result);
        $this->assertEquals('class::method', $matches[1]);

        $result = preg_match(PHPUnit_Util_Test::REGEX_DATA_PROVIDER, '@dataProvider namespace\class::method', $matches);
        $this->assertEquals(1, $result);
        $this->assertEquals('namespace\class::method', $matches[1]);

        $result = preg_match(PHPUnit_Util_Test::REGEX_DATA_PROVIDER, '@dataProvider namespace\namespace\class::method', $matches);
        $this->assertEquals(1, $result);
        $this->assertEquals('namespace\namespace\class::method', $matches[1]);

        $result = preg_match(PHPUnit_Util_Test::REGEX_DATA_PROVIDER, '@dataProvider メソッド', $matches);
        $this->assertEquals(1, $result);
        $this->assertEquals('メソッド', $matches[1]);
    }

    /**
     * @covers PHPUnit_Util_Test::getDataFromTestWithAnnotation
     */
    public function test_test_with_empty_annotation()
    {
        $result = PHPUnit_Util_Test::getDataFromTestWithAnnotation("/**\n * @anotherAnnotation\n */");
        $this->assertNull($result);
    }

    /**
     * @covers PHPUnit_Util_Test::getDataFromTestWithAnnotation
     */
    public function test_test_with_simple_case()
    {
        $result = PHPUnit_Util_Test::getDataFromTestWithAnnotation('/**
                                                                     * @testWith [1]
                                                                     */');
        $this->assertEquals([[1]], $result);
    }

    /**
     * @covers PHPUnit_Util_Test::getDataFromTestWithAnnotation
     */
    public function test_test_with_multi_line_multi_parameter_case()
    {
        $result = PHPUnit_Util_Test::getDataFromTestWithAnnotation('/**
                                                                     * @testWith [1, 2]
                                                                     * [3, 4]
                                                                     */');
        $this->assertEquals([[1, 2], [3, 4]], $result);
    }

    /**
     * @covers PHPUnit_Util_Test::getDataFromTestWithAnnotation
     */
    public function test_test_with_various_types()
    {
        $result = PHPUnit_Util_Test::getDataFromTestWithAnnotation('/**
            * @testWith ["ab"]
            *           [true]
            *           [null]
         */');
        $this->assertEquals([['ab'], [true], [null]], $result);
    }

    /**
     * @covers PHPUnit_Util_Test::getDataFromTestWithAnnotation
     */
    public function test_test_with_annotation_after()
    {
        $result = PHPUnit_Util_Test::getDataFromTestWithAnnotation('/**
                                                                     * @testWith [1]
                                                                     *           [2]
                                                                     * @annotation
                                                                     */');
        $this->assertEquals([[1], [2]], $result);
    }

    /**
     * @covers PHPUnit_Util_Test::getDataFromTestWithAnnotation
     */
    public function test_test_with_simple_text_after()
    {
        $result = PHPUnit_Util_Test::getDataFromTestWithAnnotation('/**
                                                                     * @testWith [1]
                                                                     *           [2]
                                                                     * blah blah
                                                                     */');
        $this->assertEquals([[1], [2]], $result);
    }

    /**
     * @covers PHPUnit_Util_Test::getDataFromTestWithAnnotation
     */
    public function test_test_with_character_escape()
    {
        $result = PHPUnit_Util_Test::getDataFromTestWithAnnotation('/**
                                                                     * @testWith ["\"", "\""]
                                                                     */');
        $this->assertEquals([['"', '"']], $result);
    }

    /**
     * @covers PHPUnit_Util_Test::getDataFromTestWithAnnotation
     */
    public function test_test_with_throws_proper_exception_if_dataset_cannot_be_parsed()
    {
        $this->setExpectedExceptionRegExp(
            'PHPUnit_Framework_Exception',
            '/^The dataset for the @testWith annotation cannot be parsed:/'
        );
        PHPUnit_Util_Test::getDataFromTestWithAnnotation('/**
                                                           * @testWith [s]
                                                           */');
    }

    public function test_test_with_throws_proper_exception_if_multi_line_dataset_cannot_be_parsed()
    {
        $this->setExpectedExceptionRegExp(
            'PHPUnit_Framework_Exception',
            '/^The dataset for the @testWith annotation cannot be parsed:/'
        );
        PHPUnit_Util_Test::getDataFromTestWithAnnotation('/**
                                                           * @testWith ["valid"]
                                                           *           [invalid]
                                                           */');
    }

    /**
     * @covers PHPUnit_Util_Test::getDependencies
     *
     * @todo   Not sure what this test tests (name is misleading at least)
     */
    public function test_parse_annotation()
    {
        $this->assertEquals(
            ['Foo', 'ほげ'],
            PHPUnit_Util_Test::getDependencies(get_class($this), 'methodForTestParseAnnotation')
        );
    }

    /**
     * @depends Foo
     * @depends ほげ
     *
     * @todo    Remove fixture from test class
     */
    public function methodForTestParseAnnotation() {}

    /**
     * @covers PHPUnit_Util_Test::getDependencies
     */
    public function test_parse_annotation_that_is_only_one_line()
    {
        $this->assertEquals(
            ['Bar'],
            PHPUnit_Util_Test::getDependencies(get_class($this), 'methodForTestParseAnnotationThatIsOnlyOneLine')
        );
    }

    /** @depends Bar */
    public function methodForTestParseAnnotationThatIsOnlyOneLine()
    {
        // TODO Remove fixture from test class
    }

    /**
     * @covers       PHPUnit_Util_Test::getLinesToBeCovered
     * @covers       PHPUnit_Util_Test::getLinesToBeCoveredOrUsed
     * @covers       PHPUnit_Util_Test::resolveElementToReflectionObjects
     *
     * @dataProvider getLinesToBeCoveredProvider
     */
    public function test_get_lines_to_be_covered($test, $lines)
    {
        if (strpos($test, 'Namespace') === 0) {
            $expected = [
                TEST_FILES_PATH.'NamespaceCoveredClass.php' => $lines,
            ];
        } elseif ($test === 'CoverageNoneTest') {
            $expected = [];
        } elseif ($test === 'CoverageNothingTest') {
            $expected = false;
        } elseif ($test === 'CoverageFunctionTest') {
            $expected = [
                TEST_FILES_PATH.'CoveredFunction.php' => $lines,
            ];
        } else {
            $expected = [TEST_FILES_PATH.'CoveredClass.php' => $lines];
        }

        $this->assertEquals(
            $expected,
            PHPUnit_Util_Test::getLinesToBeCovered(
                $test, 'testSomething'
            )
        );
    }

    /**
     * @covers            PHPUnit_Util_Test::getLinesToBeCovered
     * @covers            PHPUnit_Util_Test::getLinesToBeCoveredOrUsed
     * @covers            PHPUnit_Util_Test::resolveElementToReflectionObjects
     *
     * @expectedException PHPUnit_Framework_CodeCoverageException
     */
    public function test_get_lines_to_be_covered2()
    {
        PHPUnit_Util_Test::getLinesToBeCovered(
            'NotExistingCoveredElementTest', 'testOne'
        );
    }

    /**
     * @covers            PHPUnit_Util_Test::getLinesToBeCovered
     * @covers            PHPUnit_Util_Test::getLinesToBeCoveredOrUsed
     * @covers            PHPUnit_Util_Test::resolveElementToReflectionObjects
     *
     * @expectedException PHPUnit_Framework_CodeCoverageException
     */
    public function test_get_lines_to_be_covered3()
    {
        PHPUnit_Util_Test::getLinesToBeCovered(
            'NotExistingCoveredElementTest', 'testTwo'
        );
    }

    /**
     * @covers            PHPUnit_Util_Test::getLinesToBeCovered
     * @covers            PHPUnit_Util_Test::getLinesToBeCoveredOrUsed
     * @covers            PHPUnit_Util_Test::resolveElementToReflectionObjects
     *
     * @expectedException PHPUnit_Framework_CodeCoverageException
     */
    public function test_get_lines_to_be_covered4()
    {
        PHPUnit_Util_Test::getLinesToBeCovered(
            'NotExistingCoveredElementTest', 'testThree'
        );
    }

    /**
     * @covers PHPUnit_Util_Test::getLinesToBeCovered
     * @covers PHPUnit_Util_Test::getLinesToBeCoveredOrUsed
     */
    public function test_get_lines_to_be_covered_skips_non_existent_methods()
    {
        $this->assertSame(
            [],
            PHPUnit_Util_Test::getLinesToBeCovered(
                'NotExistingCoveredElementTest',
                'methodDoesNotExist'
            )
        );
    }

    /**
     * @covers            PHPUnit_Util_Test::getLinesToBeCovered
     * @covers            PHPUnit_Util_Test::getLinesToBeCoveredOrUsed
     *
     * @expectedException PHPUnit_Framework_CodeCoverageException
     */
    public function test_two_covers_default_class_annoations_are_not_allowed()
    {
        PHPUnit_Util_Test::getLinesToBeCovered(
            'CoverageTwoDefaultClassAnnotations',
            'testSomething'
        );
    }

    /**
     * @covers PHPUnit_Util_Test::getLinesToBeCovered
     * @covers PHPUnit_Util_Test::getLinesToBeCoveredOrUsed
     */
    public function test_function_parentheses_are_allowed()
    {
        $this->assertSame(
            [TEST_FILES_PATH.'CoveredFunction.php' => range(2, 4)],
            PHPUnit_Util_Test::getLinesToBeCovered(
                'CoverageFunctionParenthesesTest',
                'testSomething'
            )
        );
    }

    /**
     * @covers PHPUnit_Util_Test::getLinesToBeCovered
     * @covers PHPUnit_Util_Test::getLinesToBeCoveredOrUsed
     */
    public function test_function_parentheses_are_allowed_with_whitespace()
    {
        $this->assertSame(
            [TEST_FILES_PATH.'CoveredFunction.php' => range(2, 4)],
            PHPUnit_Util_Test::getLinesToBeCovered(
                'CoverageFunctionParenthesesWhitespaceTest',
                'testSomething'
            )
        );
    }

    /**
     * @covers PHPUnit_Util_Test::getLinesToBeCovered
     * @covers PHPUnit_Util_Test::getLinesToBeCoveredOrUsed
     */
    public function test_method_parentheses_are_allowed()
    {
        $this->assertSame(
            [TEST_FILES_PATH.'CoveredClass.php' => range(31, 35)],
            PHPUnit_Util_Test::getLinesToBeCovered(
                'CoverageMethodParenthesesTest',
                'testSomething'
            )
        );
    }

    /**
     * @covers PHPUnit_Util_Test::getLinesToBeCovered
     * @covers PHPUnit_Util_Test::getLinesToBeCoveredOrUsed
     */
    public function test_method_parentheses_are_allowed_with_whitespace()
    {
        $this->assertSame(
            [TEST_FILES_PATH.'CoveredClass.php' => range(31, 35)],
            PHPUnit_Util_Test::getLinesToBeCovered(
                'CoverageMethodParenthesesWhitespaceTest',
                'testSomething'
            )
        );
    }

    /**
     * @covers PHPUnit_Util_Test::getLinesToBeCovered
     * @covers PHPUnit_Util_Test::getLinesToBeCoveredOrUsed
     */
    public function test_namespaced_function_can_be_covered_or_used()
    {
        $this->assertEquals(
            [
                TEST_FILES_PATH.'NamespaceCoveredFunction.php' => range(4, 7),
            ],
            PHPUnit_Util_Test::getLinesToBeCovered(
                'CoverageNamespacedFunctionTest',
                'testFunc'
            )
        );
    }

    public function getLinesToBeCoveredProvider()
    {
        return [
            [
                'CoverageNoneTest',
                [],
            ],
            [
                'CoverageClassExtendedTest',
                array_merge(range(19, 36), range(2, 17)),
            ],
            [
                'CoverageClassTest',
                range(19, 36),
            ],
            [
                'CoverageMethodTest',
                range(31, 35),
            ],
            [
                'CoverageMethodOneLineAnnotationTest',
                range(31, 35),
            ],
            [
                'CoverageNotPrivateTest',
                array_merge(range(25, 29), range(31, 35)),
            ],
            [
                'CoverageNotProtectedTest',
                array_merge(range(21, 23), range(31, 35)),
            ],
            [
                'CoverageNotPublicTest',
                array_merge(range(21, 23), range(25, 29)),
            ],
            [
                'CoveragePrivateTest',
                range(21, 23),
            ],
            [
                'CoverageProtectedTest',
                range(25, 29),
            ],
            [
                'CoveragePublicTest',
                range(31, 35),
            ],
            [
                'CoverageFunctionTest',
                range(2, 4),
            ],
            [
                'NamespaceCoverageClassExtendedTest',
                array_merge(range(21, 38), range(4, 19)),
            ],
            [
                'NamespaceCoverageClassTest',
                range(21, 38),
            ],
            [
                'NamespaceCoverageMethodTest',
                range(33, 37),
            ],
            [
                'NamespaceCoverageNotPrivateTest',
                array_merge(range(27, 31), range(33, 37)),
            ],
            [
                'NamespaceCoverageNotProtectedTest',
                array_merge(range(23, 25), range(33, 37)),
            ],
            [
                'NamespaceCoverageNotPublicTest',
                array_merge(range(23, 25), range(27, 31)),
            ],
            [
                'NamespaceCoveragePrivateTest',
                range(23, 25),
            ],
            [
                'NamespaceCoverageProtectedTest',
                range(27, 31),
            ],
            [
                'NamespaceCoveragePublicTest',
                range(33, 37),
            ],
            [
                'NamespaceCoverageCoversClassTest',
                array_merge(range(23, 25), range(27, 31), range(33, 37), range(6, 8), range(10, 13), range(15, 18)),
            ],
            [
                'NamespaceCoverageCoversClassPublicTest',
                range(33, 37),
            ],
            [
                'CoverageNothingTest',
                false,
            ],
        ];
    }
}
