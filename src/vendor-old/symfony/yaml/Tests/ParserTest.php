<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\Yaml\Tests;

use PHPUnit\Framework\TestCase;
use Symfony\Component\Yaml\Exception\ParseException;
use Symfony\Component\Yaml\Parser;
use Symfony\Component\Yaml\Yaml;

class ParserTest extends TestCase
{
    /** @var Parser */
    protected $parser;

    protected function setUp()
    {
        $this->parser = new Parser;
    }

    protected function tearDown()
    {
        $this->parser = null;
    }

    /**
     * @dataProvider getDataFormSpecifications
     */
    public function test_specifications($file, $expected, $yaml, $comment, $deprecated)
    {
        $deprecations = [];

        if ($deprecated) {
            set_error_handler(function ($type, $msg) use (&$deprecations) {
                if ($type !== E_USER_DEPRECATED) {
                    restore_error_handler();

                    if (class_exists('PHPUnit_Util_ErrorHandler')) {
                        return call_user_func_array('PHPUnit_Util_ErrorHandler::handleError', func_get_args());
                    }

                    return call_user_func_array('PHPUnit\Util\ErrorHandler::handleError', func_get_args());
                }

                $deprecations[] = $msg;
            });
        }

        $this->assertEquals($expected, var_export($this->parser->parse($yaml), true), $comment);

        if ($deprecated) {
            restore_error_handler();

            $this->assertCount(1, $deprecations);
            $this->assertContains('Using the comma as a group separator for floats is deprecated since version 3.2 and will be removed in 4.0.', $deprecations[0]);
        }
    }

    public function getDataFormSpecifications()
    {
        $parser = new Parser;
        $path = __DIR__.'/Fixtures';

        $tests = [];
        $files = $parser->parse(file_get_contents($path.'/index.yml'));
        foreach ($files as $file) {
            $yamls = file_get_contents($path.'/'.$file.'.yml');

            // split YAMLs documents
            foreach (preg_split('/^---( %YAML\:1\.0)?/m', $yamls) as $yaml) {
                if (! $yaml) {
                    continue;
                }

                $test = $parser->parse($yaml);
                if (isset($test['todo']) && $test['todo']) {
                    // TODO
                } else {
                    eval('$expected = '.trim($test['php']).';');

                    $tests[] = [$file, var_export($expected, true), $test['yaml'], $test['test'], isset($test['deprecated']) ? $test['deprecated'] : false];
                }
            }
        }

        return $tests;
    }

    public function test_tabs_in_yaml()
    {
        // test tabs in YAML
        $yamls = [
            "foo:\n	bar",
            "foo:\n 	bar",
            "foo:\n	 bar",
            "foo:\n 	 bar",
        ];

        foreach ($yamls as $yaml) {
            try {
                $content = $this->parser->parse($yaml);

                $this->fail('YAML files must not contain tabs');
            } catch (\Exception $e) {
                $this->assertInstanceOf('\Exception', $e, 'YAML files must not contain tabs');
                $this->assertEquals('A YAML file cannot contain tabs as indentation at line 2 (near "'.strpbrk($yaml, "\t").'").', $e->getMessage(), 'YAML files must not contain tabs');
            }
        }
    }

    public function test_end_of_the_document_marker()
    {
        $yaml = <<<'EOF'
--- %YAML:1.0
foo
...
EOF;

        $this->assertEquals('foo', $this->parser->parse($yaml));
    }

    public function getBlockChompingTests()
    {
        $tests = [];

        $yaml = <<<'EOF'
foo: |-
    one
    two
bar: |-
    one
    two

EOF;
        $expected = [
            'foo' => "one\ntwo",
            'bar' => "one\ntwo",
        ];
        $tests['Literal block chomping strip with single trailing newline'] = [$expected, $yaml];

        $yaml = <<<'EOF'
foo: |-
    one
    two

bar: |-
    one
    two


EOF;
        $expected = [
            'foo' => "one\ntwo",
            'bar' => "one\ntwo",
        ];
        $tests['Literal block chomping strip with multiple trailing newlines'] = [$expected, $yaml];

        $yaml = <<<'EOF'
{}


EOF;
        $expected = [];
        $tests['Literal block chomping strip with multiple trailing newlines after a 1-liner'] = [$expected, $yaml];

        $yaml = <<<'EOF'
foo: |-
    one
    two
bar: |-
    one
    two
EOF;
        $expected = [
            'foo' => "one\ntwo",
            'bar' => "one\ntwo",
        ];
        $tests['Literal block chomping strip without trailing newline'] = [$expected, $yaml];

        $yaml = <<<'EOF'
foo: |
    one
    two
bar: |
    one
    two

EOF;
        $expected = [
            'foo' => "one\ntwo\n",
            'bar' => "one\ntwo\n",
        ];
        $tests['Literal block chomping clip with single trailing newline'] = [$expected, $yaml];

        $yaml = <<<'EOF'
foo: |
    one
    two

bar: |
    one
    two


EOF;
        $expected = [
            'foo' => "one\ntwo\n",
            'bar' => "one\ntwo\n",
        ];
        $tests['Literal block chomping clip with multiple trailing newlines'] = [$expected, $yaml];

        $yaml = <<<'EOF'
foo:
- bar: |
    one

    two
EOF;
        $expected = [
            'foo' => [
                [
                    'bar' => "one\n\ntwo",
                ],
            ],
        ];
        $tests['Literal block chomping clip with embedded blank line inside unindented collection'] = [$expected, $yaml];

        $yaml = <<<'EOF'
foo: |
    one
    two
bar: |
    one
    two
EOF;
        $expected = [
            'foo' => "one\ntwo\n",
            'bar' => "one\ntwo",
        ];
        $tests['Literal block chomping clip without trailing newline'] = [$expected, $yaml];

        $yaml = <<<'EOF'
foo: |+
    one
    two
bar: |+
    one
    two

EOF;
        $expected = [
            'foo' => "one\ntwo\n",
            'bar' => "one\ntwo\n",
        ];
        $tests['Literal block chomping keep with single trailing newline'] = [$expected, $yaml];

        $yaml = <<<'EOF'
foo: |+
    one
    two

bar: |+
    one
    two


EOF;
        $expected = [
            'foo' => "one\ntwo\n\n",
            'bar' => "one\ntwo\n\n",
        ];
        $tests['Literal block chomping keep with multiple trailing newlines'] = [$expected, $yaml];

        $yaml = <<<'EOF'
foo: |+
    one
    two
bar: |+
    one
    two
EOF;
        $expected = [
            'foo' => "one\ntwo\n",
            'bar' => "one\ntwo",
        ];
        $tests['Literal block chomping keep without trailing newline'] = [$expected, $yaml];

        $yaml = <<<'EOF'
foo: >-
    one
    two
bar: >-
    one
    two

EOF;
        $expected = [
            'foo' => 'one two',
            'bar' => 'one two',
        ];
        $tests['Folded block chomping strip with single trailing newline'] = [$expected, $yaml];

        $yaml = <<<'EOF'
foo: >-
    one
    two

bar: >-
    one
    two


EOF;
        $expected = [
            'foo' => 'one two',
            'bar' => 'one two',
        ];
        $tests['Folded block chomping strip with multiple trailing newlines'] = [$expected, $yaml];

        $yaml = <<<'EOF'
foo: >-
    one
    two
bar: >-
    one
    two
EOF;
        $expected = [
            'foo' => 'one two',
            'bar' => 'one two',
        ];
        $tests['Folded block chomping strip without trailing newline'] = [$expected, $yaml];

        $yaml = <<<'EOF'
foo: >
    one
    two
bar: >
    one
    two

EOF;
        $expected = [
            'foo' => "one two\n",
            'bar' => "one two\n",
        ];
        $tests['Folded block chomping clip with single trailing newline'] = [$expected, $yaml];

        $yaml = <<<'EOF'
foo: >
    one
    two

bar: >
    one
    two


EOF;
        $expected = [
            'foo' => "one two\n",
            'bar' => "one two\n",
        ];
        $tests['Folded block chomping clip with multiple trailing newlines'] = [$expected, $yaml];

        $yaml = <<<'EOF'
foo: >
    one
    two
bar: >
    one
    two
EOF;
        $expected = [
            'foo' => "one two\n",
            'bar' => 'one two',
        ];
        $tests['Folded block chomping clip without trailing newline'] = [$expected, $yaml];

        $yaml = <<<'EOF'
foo: >+
    one
    two
bar: >+
    one
    two

EOF;
        $expected = [
            'foo' => "one two\n",
            'bar' => "one two\n",
        ];
        $tests['Folded block chomping keep with single trailing newline'] = [$expected, $yaml];

        $yaml = <<<'EOF'
foo: >+
    one
    two

bar: >+
    one
    two


EOF;
        $expected = [
            'foo' => "one two\n\n",
            'bar' => "one two\n\n",
        ];
        $tests['Folded block chomping keep with multiple trailing newlines'] = [$expected, $yaml];

        $yaml = <<<'EOF'
foo: >+
    one
    two
bar: >+
    one
    two
EOF;
        $expected = [
            'foo' => "one two\n",
            'bar' => 'one two',
        ];
        $tests['Folded block chomping keep without trailing newline'] = [$expected, $yaml];

        return $tests;
    }

    /**
     * @dataProvider getBlockChompingTests
     */
    public function test_block_chomping($expected, $yaml)
    {
        $this->assertSame($expected, $this->parser->parse($yaml));
    }

    /**
     * Regression test for issue #7989.
     *
     * @see https://github.com/symfony/symfony/issues/7989
     */
    public function test_block_literal_with_leading_newlines()
    {
        $yaml = <<<'EOF'
foo: |-


    bar

EOF;
        $expected = [
            'foo' => "\n\nbar",
        ];

        $this->assertSame($expected, $this->parser->parse($yaml));
    }

    public function test_object_support_enabled()
    {
        $input = <<<'EOF'
foo: !php/object:O:30:"Symfony\Component\Yaml\Tests\B":1:{s:1:"b";s:3:"foo";}
bar: 1
EOF;
        $this->assertEquals(['foo' => new B, 'bar' => 1], $this->parser->parse($input, Yaml::PARSE_OBJECT), '->parse() is able to parse objects');
    }

    /**
     * @group legacy
     */
    public function test_object_support_enabled_passing_true()
    {
        $input = <<<'EOF'
foo: !php/object:O:30:"Symfony\Component\Yaml\Tests\B":1:{s:1:"b";s:3:"foo";}
bar: 1
EOF;
        $this->assertEquals(['foo' => new B, 'bar' => 1], $this->parser->parse($input, false, true), '->parse() is able to parse objects');
    }

    /**
     * @group legacy
     */
    public function test_object_support_enabled_with_deprecated_tag()
    {
        $input = <<<'EOF'
foo: !!php/object:O:30:"Symfony\Component\Yaml\Tests\B":1:{s:1:"b";s:3:"foo";}
bar: 1
EOF;
        $this->assertEquals(['foo' => new B, 'bar' => 1], $this->parser->parse($input, Yaml::PARSE_OBJECT), '->parse() is able to parse objects');
    }

    /**
     * @dataProvider invalidDumpedObjectProvider
     */
    public function test_object_support_disabled_but_no_exceptions($input)
    {
        $this->assertEquals(['foo' => null, 'bar' => 1], $this->parser->parse($input), '->parse() does not parse objects');
    }

    /**
     * @dataProvider getObjectForMapTests
     */
    public function test_object_for_map($yaml, $expected)
    {
        $this->assertEquals($expected, $this->parser->parse($yaml, Yaml::PARSE_OBJECT_FOR_MAP));
    }

    /**
     * @group legacy
     *
     * @dataProvider getObjectForMapTests
     */
    public function test_object_for_map_enabled_with_mapping_using_boolean_toggles($yaml, $expected)
    {
        $this->assertEquals($expected, $this->parser->parse($yaml, false, false, true));
    }

    public function getObjectForMapTests()
    {
        $tests = [];

        $yaml = <<<'EOF'
foo:
    fiz: [cat]
EOF;
        $expected = new \stdClass;
        $expected->foo = new \stdClass;
        $expected->foo->fiz = ['cat'];
        $tests['mapping'] = [$yaml, $expected];

        $yaml = '{ "foo": "bar", "fiz": "cat" }';
        $expected = new \stdClass;
        $expected->foo = 'bar';
        $expected->fiz = 'cat';
        $tests['inline-mapping'] = [$yaml, $expected];

        $yaml = "foo: bar\nbaz: foobar";
        $expected = new \stdClass;
        $expected->foo = 'bar';
        $expected->baz = 'foobar';
        $tests['object-for-map-is-applied-after-parsing'] = [$yaml, $expected];

        $yaml = <<<'EOT'
array:
  - key: one
  - key: two
EOT;
        $expected = new \stdClass;
        $expected->array = [];
        $expected->array[0] = new \stdClass;
        $expected->array[0]->key = 'one';
        $expected->array[1] = new \stdClass;
        $expected->array[1]->key = 'two';
        $tests['nest-map-and-sequence'] = [$yaml, $expected];

        $yaml = <<<'YAML'
map:
  1: one
  2: two
YAML;
        $expected = new \stdClass;
        $expected->map = new \stdClass;
        $expected->map->{1} = 'one';
        $expected->map->{2} = 'two';
        $tests['numeric-keys'] = [$yaml, $expected];

        $yaml = <<<'YAML'
map:
  0: one
  1: two
YAML;
        $expected = new \stdClass;
        $expected->map = new \stdClass;
        $expected->map->{0} = 'one';
        $expected->map->{1} = 'two';
        $tests['zero-indexed-numeric-keys'] = [$yaml, $expected];

        return $tests;
    }

    /**
     * @dataProvider invalidDumpedObjectProvider
     *
     * @expectedException \Symfony\Component\Yaml\Exception\ParseException
     */
    public function test_objects_support_disabled_with_exceptions($yaml)
    {
        $this->parser->parse($yaml, Yaml::PARSE_EXCEPTION_ON_INVALID_TYPE);
    }

    /**
     * @group legacy
     *
     * @dataProvider invalidDumpedObjectProvider
     *
     * @expectedException \Symfony\Component\Yaml\Exception\ParseException
     */
    public function test_objects_support_disabled_with_exceptions_using_boolean_toggles($yaml)
    {
        $this->parser->parse($yaml, true);
    }

    public function invalidDumpedObjectProvider()
    {
        $yamlTag = <<<'EOF'
foo: !!php/object:O:30:"Symfony\Tests\Component\Yaml\B":1:{s:1:"b";s:3:"foo";}
bar: 1
EOF;
        $localTag = <<<'EOF'
foo: !php/object:O:30:"Symfony\Tests\Component\Yaml\B":1:{s:1:"b";s:3:"foo";}
bar: 1
EOF;

        return [
            'yaml-tag' => [$yamlTag],
            'local-tag' => [$localTag],
        ];
    }

    /**
     * @requires extension iconv
     */
    public function test_non_utf8_exception()
    {
        $yamls = [
            iconv('UTF-8', 'ISO-8859-1', "foo: 'äöüß'"),
            iconv('UTF-8', 'ISO-8859-15', "euro: '€'"),
            iconv('UTF-8', 'CP1252', "cp1252: '©ÉÇáñ'"),
        ];

        foreach ($yamls as $yaml) {
            try {
                $this->parser->parse($yaml);

                $this->fail('charsets other than UTF-8 are rejected.');
            } catch (\Exception $e) {
                $this->assertInstanceOf('Symfony\Component\Yaml\Exception\ParseException', $e, 'charsets other than UTF-8 are rejected.');
            }
        }
    }

    /**
     * @expectedException \Symfony\Component\Yaml\Exception\ParseException
     */
    public function test_unindented_collection_exception()
    {
        $yaml = <<<'EOF'

collection:
-item1
-item2
-item3

EOF;

        $this->parser->parse($yaml);
    }

    /**
     * @expectedException \Symfony\Component\Yaml\Exception\ParseException
     */
    public function test_shortcut_key_unindented_collection_exception()
    {
        $yaml = <<<'EOF'

collection:
-  key: foo
  foo: bar

EOF;

        $this->parser->parse($yaml);
    }

    /**
     * @expectedException \Symfony\Component\Yaml\Exception\ParseException
     *
     * @expectedExceptionMessageRegExp /^Multiple documents are not supported.+/
     */
    public function test_multiple_documents_not_supported_exception()
    {
        Yaml::parse(<<<'EOL'
# Ranking of 1998 home runs
---
- Mark McGwire
- Sammy Sosa
- Ken Griffey

# Team ranking
---
- Chicago Cubs
- St Louis Cardinals
EOL
        );
    }

    /**
     * @expectedException \Symfony\Component\Yaml\Exception\ParseException
     */
    public function test_sequence_in_a_mapping()
    {
        Yaml::parse(<<<'EOF'
yaml:
  hash: me
  - array stuff
EOF
        );
    }

    public function test_sequence_in_mapping_started_by_single_dash_line()
    {
        $yaml = <<<'EOT'
a:
-
  b:
  -
    bar: baz
- foo
d: e
EOT;
        $expected = [
            'a' => [
                [
                    'b' => [
                        [
                            'bar' => 'baz',
                        ],
                    ],
                ],
                'foo',
            ],
            'd' => 'e',
        ];

        $this->assertSame($expected, $this->parser->parse($yaml));
    }

    public function test_sequence_followed_by_comment_embedded_in_mapping()
    {
        $yaml = <<<'EOT'
a:
    b:
        - c
# comment
    d: e
EOT;
        $expected = [
            'a' => [
                'b' => ['c'],
                'd' => 'e',
            ],
        ];

        $this->assertSame($expected, $this->parser->parse($yaml));
    }

    /**
     * @expectedException \Symfony\Component\Yaml\Exception\ParseException
     */
    public function test_mapping_in_a_sequence()
    {
        Yaml::parse(<<<'EOF'
yaml:
  - array stuff
  hash: me
EOF
        );
    }

    /**
     * @expectedException \Symfony\Component\Yaml\Exception\ParseException
     *
     * @expectedExceptionMessage missing colon
     */
    public function test_scalar_in_sequence()
    {
        Yaml::parse(<<<'EOF'
foo:
    - bar
"missing colon"
    foo: bar
EOF
        );
    }

    /**
     * > It is an error for two equal keys to appear in the same mapping node.
     * > In such a case the YAML processor may continue, ignoring the second
     * > `key: value` pair and issuing an appropriate warning. This strategy
     * > preserves a consistent information model for one-pass and random access
     * > applications.
     *
     * @see http://yaml.org/spec/1.2/spec.html#id2759572
     * @see http://yaml.org/spec/1.1/#id932806
     *
     * @group legacy
     */
    public function test_mapping_duplicate_key_block()
    {
        $input = <<<'EOD'
parent:
    child: first
    child: duplicate
parent:
    child: duplicate
    child: duplicate
EOD;
        $expected = [
            'parent' => [
                'child' => 'first',
            ],
        ];
        $this->assertSame($expected, Yaml::parse($input));
    }

    /**
     * @group legacy
     */
    public function test_mapping_duplicate_key_flow()
    {
        $input = <<<'EOD'
parent: { child: first, child: duplicate }
parent: { child: duplicate, child: duplicate }
EOD;
        $expected = [
            'parent' => [
                'child' => 'first',
            ],
        ];
        $this->assertSame($expected, Yaml::parse($input));
    }

    /**
     * @group legacy
     *
     * @dataProvider getParseExceptionOnDuplicateData
     *
     * @expectedDeprecation Duplicate key "%s" detected on line %d whilst parsing YAML. Silent handling of duplicate mapping keys in YAML is deprecated %s.
     * throws \Symfony\Component\Yaml\Exception\ParseException in 4.0
     */
    public function test_parse_exception_on_duplicate($input, $duplicateKey, $lineNumber)
    {
        Yaml::parse($input);
    }

    public function getParseExceptionOnDuplicateData()
    {
        $tests = [];

        $yaml = <<<'EOD'
parent: { child: first, child: duplicate }
EOD;
        $tests[] = [$yaml, 'child', 1];

        $yaml = <<<'EOD'
parent:
  child: first,
  child: duplicate
EOD;
        $tests[] = [$yaml, 'child', 3];

        $yaml = <<<'EOD'
parent: { child: foo }
parent: { child: bar }
EOD;
        $tests[] = [$yaml, 'parent', 2];

        $yaml = <<<'EOD'
parent: { child_mapping: { value: bar},  child_mapping: { value: bar} }
EOD;
        $tests[] = [$yaml, 'child_mapping', 1];

        $yaml = <<<'EOD'
parent:
  child_mapping:
    value: bar
  child_mapping:
    value: bar
EOD;
        $tests[] = [$yaml, 'child_mapping', 4];

        $yaml = <<<'EOD'
parent: { child_sequence: ['key1', 'key2', 'key3'],  child_sequence: ['key1', 'key2', 'key3'] }
EOD;
        $tests[] = [$yaml, 'child_sequence', 1];

        $yaml = <<<'EOD'
parent:
  child_sequence:
    - key1
    - key2
    - key3
  child_sequence:
    - key1
    - key2
    - key3
EOD;
        $tests[] = [$yaml, 'child_sequence', 6];

        return $tests;
    }

    public function test_empty_value()
    {
        $input = <<<'EOF'
hash:
EOF;

        $this->assertEquals(['hash' => null], Yaml::parse($input));
    }

    public function test_comment_at_the_root_indent()
    {
        $this->assertEquals([
            'services' => [
                'app.foo_service' => [
                    'class' => 'Foo',
                ],
                'app/bar_service' => [
                    'class' => 'Bar',
                ],
            ],
        ], Yaml::parse(<<<'EOF'
# comment 1
services:
# comment 2
    # comment 3
    app.foo_service:
        class: Foo
# comment 4
    # comment 5
    app/bar_service:
        class: Bar
EOF
        ));
    }

    public function test_string_block_with_comments()
    {
        $this->assertEquals(['content' => <<<'EOT'
# comment 1
header

    # comment 2
    <body>
        <h1>title</h1>
    </body>

footer # comment3
EOT
        ], Yaml::parse(<<<'EOF'
content: |
    # comment 1
    header

        # comment 2
        <body>
            <h1>title</h1>
        </body>

    footer # comment3
EOF
        ));
    }

    public function test_folded_string_block_with_comments()
    {
        $this->assertEquals([['content' => <<<'EOT'
# comment 1
header

    # comment 2
    <body>
        <h1>title</h1>
    </body>

footer # comment3
EOT
        ]], Yaml::parse(<<<'EOF'
-
    content: |
        # comment 1
        header

            # comment 2
            <body>
                <h1>title</h1>
            </body>

        footer # comment3
EOF
        ));
    }

    public function test_nested_folded_string_block_with_comments()
    {
        $this->assertEquals([[
            'title' => 'some title',
            'content' => <<<'EOT'
# comment 1
header

    # comment 2
    <body>
        <h1>title</h1>
    </body>

footer # comment3
EOT
        ]], Yaml::parse(<<<'EOF'
-
    title: some title
    content: |
        # comment 1
        header

            # comment 2
            <body>
                <h1>title</h1>
            </body>

        footer # comment3
EOF
        ));
    }

    public function test_reference_resolving_in_inline_strings()
    {
        $this->assertEquals([
            'var' => 'var-value',
            'scalar' => 'var-value',
            'list' => ['var-value'],
            'list_in_list' => [['var-value']],
            'map_in_list' => [['key' => 'var-value']],
            'embedded_mapping' => [['key' => 'var-value']],
            'map' => ['key' => 'var-value'],
            'list_in_map' => ['key' => ['var-value']],
            'map_in_map' => ['foo' => ['bar' => 'var-value']],
        ], Yaml::parse(<<<'EOF'
var:  &var var-value
scalar: *var
list: [ *var ]
list_in_list: [[ *var ]]
map_in_list: [ { key: *var } ]
embedded_mapping: [ key: *var ]
map: { key: *var }
list_in_map: { key: [*var] }
map_in_map: { foo: { bar: *var } }
EOF
        ));
    }

    public function test_yaml_directive()
    {
        $yaml = <<<'EOF'
%YAML 1.2
---
foo: 1
bar: 2
EOF;
        $this->assertEquals(['foo' => 1, 'bar' => 2], $this->parser->parse($yaml));
    }

    public function test_float_keys()
    {
        $yaml = <<<'EOF'
foo:
    1.2: "bar"
    1.3: "baz"
EOF;

        $expected = [
            'foo' => [
                '1.2' => 'bar',
                '1.3' => 'baz',
            ],
        ];

        $this->assertEquals($expected, $this->parser->parse($yaml));
    }

    /**
     * @expectedException \Symfony\Component\Yaml\Exception\ParseException
     *
     * @expectedExceptionMessage A colon cannot be used in an unquoted mapping value
     */
    public function test_colon_in_mapping_value_exception()
    {
        $yaml = <<<'EOF'
foo: bar: baz
EOF;

        $this->parser->parse($yaml);
    }

    public function test_colon_in_mapping_value_exception_not_triggered_by_colon_in_comment()
    {
        $yaml = <<<'EOT'
foo:
    bar: foobar # Note: a comment after a colon
EOT;

        $this->assertSame(['foo' => ['bar' => 'foobar']], $this->parser->parse($yaml));
    }

    /**
     * @dataProvider getCommentLikeStringInScalarBlockData
     */
    public function test_comment_like_strings_are_not_stripped_in_block_scalars($yaml, $expectedParserResult)
    {
        $this->assertSame($expectedParserResult, $this->parser->parse($yaml));
    }

    public function getCommentLikeStringInScalarBlockData()
    {
        $tests = [];

        $yaml = <<<'EOT'
pages:
    -
        title: some title
        content: |
            # comment 1
            header

                # comment 2
                <body>
                    <h1>title</h1>
                </body>

            footer # comment3
EOT;
        $expected = [
            'pages' => [
                [
                    'title' => 'some title',
                    'content' => <<<'EOT'
# comment 1
header

    # comment 2
    <body>
        <h1>title</h1>
    </body>

footer # comment3
EOT
                    ,
                ],
            ],
        ];
        $tests[] = [$yaml, $expected];

        $yaml = <<<'EOT'
test: |
    foo
    # bar
    baz
collection:
    - one: |
        foo
        # bar
        baz
    - two: |
        foo
        # bar
        baz
EOT;
        $expected = [
            'test' => <<<'EOT'
foo
# bar
baz

EOT
            ,
            'collection' => [
                [
                    'one' => <<<'EOT'
foo
# bar
baz

EOT
                    ,
                ],
                [
                    'two' => <<<'EOT'
foo
# bar
baz
EOT
                    ,
                ],
            ],
        ];
        $tests[] = [$yaml, $expected];

        $yaml = <<<'EOT'
foo:
  bar:
    scalar-block: >
      line1
      line2>
  baz:
# comment
    foobar: ~
EOT;
        $expected = [
            'foo' => [
                'bar' => [
                    'scalar-block' => "line1 line2>\n",
                ],
                'baz' => [
                    'foobar' => null,
                ],
            ],
        ];
        $tests[] = [$yaml, $expected];

        $yaml = <<<'EOT'
a:
    b: hello
#    c: |
#        first row
#        second row
    d: hello
EOT;
        $expected = [
            'a' => [
                'b' => 'hello',
                'd' => 'hello',
            ],
        ];
        $tests[] = [$yaml, $expected];

        return $tests;
    }

    public function test_blank_lines_are_parsed_as_new_lines_in_folded_blocks()
    {
        $yaml = <<<'EOT'
test: >
    <h2>A heading</h2>

    <ul>
    <li>a list</li>
    <li>may be a good example</li>
    </ul>
EOT;

        $this->assertSame(
            [
                'test' => <<<'EOT'
<h2>A heading</h2>
<ul> <li>a list</li> <li>may be a good example</li> </ul>
EOT
                ,
            ],
            $this->parser->parse($yaml)
        );
    }

    public function test_additionally_indented_lines_are_parsed_as_new_lines_in_folded_blocks()
    {
        $yaml = <<<'EOT'
test: >
    <h2>A heading</h2>

    <ul>
      <li>a list</li>
      <li>may be a good example</li>
    </ul>
EOT;

        $this->assertSame(
            [
                'test' => <<<'EOT'
<h2>A heading</h2>
<ul>
  <li>a list</li>
  <li>may be a good example</li>
</ul>
EOT
                ,
            ],
            $this->parser->parse($yaml)
        );
    }

    /**
     * @dataProvider getBinaryData
     */
    public function test_parse_binary_data($data)
    {
        $this->assertSame(['data' => 'Hello world'], $this->parser->parse($data));
    }

    public function getBinaryData()
    {
        return [
            'enclosed with double quotes' => ['data: !!binary "SGVsbG8gd29ybGQ="'],
            'enclosed with single quotes' => ["data: !!binary 'SGVsbG8gd29ybGQ='"],
            'containing spaces' => ['data: !!binary  "SGVs bG8gd 29ybGQ="'],
            'in block scalar' => [
                <<<'EOT'
data: !!binary |
    SGVsbG8gd29ybGQ=
EOT
            ],
            'containing spaces in block scalar' => [
                <<<'EOT'
data: !!binary |
    SGVs bG8gd 29ybGQ=
EOT
            ],
        ];
    }

    /**
     * @dataProvider getInvalidBinaryData
     *
     * @expectedException \Symfony\Component\Yaml\Exception\ParseException
     */
    public function test_parse_invalid_binary_data($data, $expectedMessage)
    {
        if (method_exists($this, 'expectException')) {
            $this->expectExceptionMessageRegExp($expectedMessage);
        } else {
            $this->setExpectedExceptionRegExp(ParseException::class, $expectedMessage);
        }

        $this->parser->parse($data);
    }

    public function getInvalidBinaryData()
    {
        return [
            'length not a multiple of four' => ['data: !!binary "SGVsbG8d29ybGQ="', '/The normalized base64 encoded data \(data without whitespace characters\) length must be a multiple of four \(\d+ bytes given\)/'],
            'invalid characters' => ['!!binary "SGVsbG8#d29ybGQ="', '/The base64 encoded data \(.*\) contains invalid characters/'],
            'too many equals characters' => ['data: !!binary "SGVsbG8gd29yb==="', '/The base64 encoded data \(.*\) contains invalid characters/'],
            'misplaced equals character' => ['data: !!binary "SGVsbG8gd29ybG=Q"', '/The base64 encoded data \(.*\) contains invalid characters/'],
            'length not a multiple of four in block scalar' => [
                <<<'EOT'
data: !!binary |
    SGVsbG8d29ybGQ=
EOT
                ,
                '/The normalized base64 encoded data \(data without whitespace characters\) length must be a multiple of four \(\d+ bytes given\)/',
            ],
            'invalid characters in block scalar' => [
                <<<'EOT'
data: !!binary |
    SGVsbG8#d29ybGQ=
EOT
                ,
                '/The base64 encoded data \(.*\) contains invalid characters/',
            ],
            'too many equals characters in block scalar' => [
                <<<'EOT'
data: !!binary |
    SGVsbG8gd29yb===
EOT
                ,
                '/The base64 encoded data \(.*\) contains invalid characters/',
            ],
            'misplaced equals character in block scalar' => [
                <<<'EOT'
data: !!binary |
    SGVsbG8gd29ybG=Q
EOT
                ,
                '/The base64 encoded data \(.*\) contains invalid characters/',
            ],
        ];
    }

    public function test_parse_date_as_mapping_value()
    {
        $yaml = <<<'EOT'
date: 2002-12-14
EOT;
        $expectedDate = new \DateTime;
        $expectedDate->setTimeZone(new \DateTimeZone('UTC'));
        $expectedDate->setDate(2002, 12, 14);
        $expectedDate->setTime(0, 0, 0);

        $this->assertEquals(['date' => $expectedDate], $this->parser->parse($yaml, Yaml::PARSE_DATETIME));
    }

    /**
     * @dataProvider parserThrowsExceptionWithCorrectLineNumberProvider
     */
    public function test_parser_throws_exception_with_correct_line_number($lineNumber, $yaml)
    {
        if (method_exists($this, 'expectException')) {
            $this->expectException('\Symfony\Component\Yaml\Exception\ParseException');
            $this->expectExceptionMessage(sprintf('Unexpected characters near "," at line %d (near "bar: "123",").', $lineNumber));
        } else {
            $this->setExpectedException('\Symfony\Component\Yaml\Exception\ParseException', sprintf('Unexpected characters near "," at line %d (near "bar: "123",").', $lineNumber));
        }

        $this->parser->parse($yaml);
    }

    public function parserThrowsExceptionWithCorrectLineNumberProvider()
    {
        return [
            [
                4,
                <<<'YAML'
foo:
    -
        # bar
        bar: "123",
YAML
            ],
            [
                5,
                <<<'YAML'
foo:
    -
        # bar
        # bar
        bar: "123",
YAML
            ],
            [
                8,
                <<<'YAML'
foo:
    -
        # foobar
        baz: 123
bar:
    -
        # bar
        bar: "123",
YAML
            ],
            [
                10,
                <<<'YAML'
foo:
    -
        # foobar
        # foobar
        baz: 123
bar:
    -
        # bar
        # bar
        bar: "123",
YAML
            ],
        ];
    }

    public function test_parse_multi_line_quoted_string()
    {
        $yaml = <<<'EOT'
foo: "bar
  baz
   foobar
foo"
bar: baz
EOT;

        $this->assertSame(['foo' => 'bar baz foobar foo', 'bar' => 'baz'], $this->parser->parse($yaml));
    }

    public function test_parse_multi_line_unquoted_string()
    {
        $yaml = <<<'EOT'
foo: bar
  baz
   foobar
  foo
bar: baz
EOT;

        $this->assertSame(['foo' => 'bar baz foobar foo', 'bar' => 'baz'], $this->parser->parse($yaml));
    }

    public function test_can_parse_very_long_value()
    {
        $longStringWithSpaces = str_repeat('xxxxxxxxxxxxxxxxxxxxxxxxxxxxxx ', 20000);
        $trickyVal = ['x' => $longStringWithSpaces];

        $yamlString = Yaml::dump($trickyVal);
        $arrayFromYaml = $this->parser->parse($yamlString);

        $this->assertEquals($trickyVal, $arrayFromYaml);
    }
}

class B
{
    public $b = 'foo';
}
