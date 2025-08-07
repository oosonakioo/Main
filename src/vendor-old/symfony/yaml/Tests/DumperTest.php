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
use Symfony\Component\Yaml\Dumper;
use Symfony\Component\Yaml\Parser;
use Symfony\Component\Yaml\Yaml;

class DumperTest extends TestCase
{
    protected $parser;

    protected $dumper;

    protected $path;

    protected $array = [
        '' => 'bar',
        'foo' => '#bar',
        'foo\'bar' => [],
        'bar' => [1, 'foo'],
        'foobar' => [
            'foo' => 'bar',
            'bar' => [1, 'foo'],
            'foobar' => [
                'foo' => 'bar',
                'bar' => [1, 'foo'],
            ],
        ],
    ];

    protected function setUp()
    {
        $this->parser = new Parser;
        $this->dumper = new Dumper;
        $this->path = __DIR__.'/Fixtures';
    }

    protected function tearDown()
    {
        $this->parser = null;
        $this->dumper = null;
        $this->path = null;
        $this->array = null;
    }

    public function test_indentation_in_constructor()
    {
        $dumper = new Dumper(7);
        $expected = <<<'EOF'
'': bar
foo: '#bar'
'foo''bar': {  }
bar:
       - 1
       - foo
foobar:
       foo: bar
       bar:
              - 1
              - foo
       foobar:
              foo: bar
              bar:
                     - 1
                     - foo

EOF;
        $this->assertEquals($expected, $dumper->dump($this->array, 4, 0));
    }

    /**
     * @group legacy
     */
    public function test_set_indentation()
    {
        $this->dumper->setIndentation(7);

        $expected = <<<'EOF'
'': bar
foo: '#bar'
'foo''bar': {  }
bar:
       - 1
       - foo
foobar:
       foo: bar
       bar:
              - 1
              - foo
       foobar:
              foo: bar
              bar:
                     - 1
                     - foo

EOF;
        $this->assertEquals($expected, $this->dumper->dump($this->array, 4, 0));
    }

    public function test_specifications()
    {
        $files = $this->parser->parse(file_get_contents($this->path.'/index.yml'));
        foreach ($files as $file) {
            $yamls = file_get_contents($this->path.'/'.$file.'.yml');

            // split YAMLs documents
            foreach (preg_split('/^---( %YAML\:1\.0)?/m', $yamls) as $yaml) {
                if (! $yaml) {
                    continue;
                }

                $test = $this->parser->parse($yaml);
                if (isset($test['dump_skip']) && $test['dump_skip']) {
                    continue;
                } elseif (isset($test['todo']) && $test['todo']) {
                    // TODO
                } else {
                    eval('$expected = '.trim($test['php']).';');
                    $this->assertSame($expected, $this->parser->parse($this->dumper->dump($expected, 10)), $test['test']);
                }
            }
        }
    }

    public function test_inline_level()
    {
        $expected = <<<'EOF'
{ '': bar, foo: '#bar', 'foo''bar': {  }, bar: [1, foo], foobar: { foo: bar, bar: [1, foo], foobar: { foo: bar, bar: [1, foo] } } }
EOF;
        $this->assertEquals($expected, $this->dumper->dump($this->array, -10), '->dump() takes an inline level argument');
        $this->assertEquals($expected, $this->dumper->dump($this->array, 0), '->dump() takes an inline level argument');

        $expected = <<<'EOF'
'': bar
foo: '#bar'
'foo''bar': {  }
bar: [1, foo]
foobar: { foo: bar, bar: [1, foo], foobar: { foo: bar, bar: [1, foo] } }

EOF;
        $this->assertEquals($expected, $this->dumper->dump($this->array, 1), '->dump() takes an inline level argument');

        $expected = <<<'EOF'
'': bar
foo: '#bar'
'foo''bar': {  }
bar:
    - 1
    - foo
foobar:
    foo: bar
    bar: [1, foo]
    foobar: { foo: bar, bar: [1, foo] }

EOF;
        $this->assertEquals($expected, $this->dumper->dump($this->array, 2), '->dump() takes an inline level argument');

        $expected = <<<'EOF'
'': bar
foo: '#bar'
'foo''bar': {  }
bar:
    - 1
    - foo
foobar:
    foo: bar
    bar:
        - 1
        - foo
    foobar:
        foo: bar
        bar: [1, foo]

EOF;
        $this->assertEquals($expected, $this->dumper->dump($this->array, 3), '->dump() takes an inline level argument');

        $expected = <<<'EOF'
'': bar
foo: '#bar'
'foo''bar': {  }
bar:
    - 1
    - foo
foobar:
    foo: bar
    bar:
        - 1
        - foo
    foobar:
        foo: bar
        bar:
            - 1
            - foo

EOF;
        $this->assertEquals($expected, $this->dumper->dump($this->array, 4), '->dump() takes an inline level argument');
        $this->assertEquals($expected, $this->dumper->dump($this->array, 10), '->dump() takes an inline level argument');
    }

    public function test_object_support_enabled()
    {
        $dump = $this->dumper->dump(['foo' => new A, 'bar' => 1], 0, 0, Yaml::DUMP_OBJECT);

        $this->assertEquals('{ foo: !php/object:O:30:"Symfony\Component\Yaml\Tests\A":1:{s:1:"a";s:3:"foo";}, bar: 1 }', $dump, '->dump() is able to dump objects');
    }

    /**
     * @group legacy
     */
    public function test_object_support_enabled_passing_true()
    {
        $dump = $this->dumper->dump(['foo' => new A, 'bar' => 1], 0, 0, false, true);

        $this->assertEquals('{ foo: !php/object:O:30:"Symfony\Component\Yaml\Tests\A":1:{s:1:"a";s:3:"foo";}, bar: 1 }', $dump, '->dump() is able to dump objects');
    }

    public function test_object_support_disabled_but_no_exceptions()
    {
        $dump = $this->dumper->dump(['foo' => new A, 'bar' => 1]);

        $this->assertEquals('{ foo: null, bar: 1 }', $dump, '->dump() does not dump objects when disabled');
    }

    /**
     * @expectedException \Symfony\Component\Yaml\Exception\DumpException
     */
    public function test_object_support_disabled_with_exceptions()
    {
        $this->dumper->dump(['foo' => new A, 'bar' => 1], 0, 0, Yaml::DUMP_EXCEPTION_ON_INVALID_TYPE);
    }

    /**
     * @group legacy
     *
     * @expectedException \Symfony\Component\Yaml\Exception\DumpException
     */
    public function test_object_support_disabled_with_exceptions_passing_true()
    {
        $this->dumper->dump(['foo' => new A, 'bar' => 1], 0, 0, true);
    }

    /**
     * @dataProvider getEscapeSequences
     */
    public function test_escaped_escape_sequences_in_quoted_scalar($input, $expected)
    {
        $this->assertEquals($expected, $this->dumper->dump($input));
    }

    public function getEscapeSequences()
    {
        return [
            'empty string' => ['', "''"],
            'null' => ["\x0", '"\\0"'],
            'bell' => ["\x7", '"\\a"'],
            'backspace' => ["\x8", '"\\b"'],
            'horizontal-tab' => ["\t", '"\\t"'],
            'line-feed' => ["\n", '"\\n"'],
            'vertical-tab' => ["\v", '"\\v"'],
            'form-feed' => ["\xC", '"\\f"'],
            'carriage-return' => ["\r", '"\\r"'],
            'escape' => ["\x1B", '"\\e"'],
            'space' => [' ', "' '"],
            'double-quote' => ['"', "'\"'"],
            'slash' => ['/', '/'],
            'backslash' => ['\\', '\\'],
            'next-line' => ["\xC2\x85", '"\\N"'],
            'non-breaking-space' => ["\xc2\xa0", '"\\_"'],
            'line-separator' => ["\xE2\x80\xA8", '"\\L"'],
            'paragraph-separator' => ["\xE2\x80\xA9", '"\\P"'],
            'colon' => [':', "':'"],
        ];
    }

    public function test_binary_data_is_dumped_base64_encoded()
    {
        $binaryData = file_get_contents(__DIR__.'/Fixtures/arrow.gif');
        $expected = '{ data: !!binary '.base64_encode($binaryData).' }';

        $this->assertSame($expected, $this->dumper->dump(['data' => $binaryData]));
    }

    public function test_non_utf8_data_is_dumped_base64_encoded()
    {
        // "für" (ISO-8859-1 encoded)
        $this->assertSame('!!binary ZsM/cg==', $this->dumper->dump("f\xc3\x3fr"));
    }

    /**
     * @dataProvider objectAsMapProvider
     */
    public function test_dump_object_as_map($object, $expected)
    {
        $yaml = $this->dumper->dump($object, 0, 0, Yaml::DUMP_OBJECT_AS_MAP);

        $this->assertEquals($expected, Yaml::parse($yaml, Yaml::PARSE_OBJECT_FOR_MAP));
    }

    public function objectAsMapProvider()
    {
        $tests = [];

        $bar = new \stdClass;
        $bar->class = 'classBar';
        $bar->args = ['bar'];
        $zar = new \stdClass;
        $foo = new \stdClass;
        $foo->bar = $bar;
        $foo->zar = $zar;
        $object = new \stdClass;
        $object->foo = $foo;
        $tests['stdClass'] = [$object, $object];

        $arrayObject = new \ArrayObject;
        $arrayObject['foo'] = 'bar';
        $arrayObject['baz'] = 'foobar';
        $parsedArrayObject = new \stdClass;
        $parsedArrayObject->foo = 'bar';
        $parsedArrayObject->baz = 'foobar';
        $tests['ArrayObject'] = [$arrayObject, $parsedArrayObject];

        $a = new A;
        $tests['arbitrary-object'] = [$a, null];

        return $tests;
    }

    public function test_dump_multi_line_string_as_scalar_block()
    {
        $data = [
            'data' => [
                'single_line' => 'foo bar baz',
                'multi_line' => "foo\nline with trailing spaces:\n  \nbar\r\ninteger like line:\n123456789\nempty line:\n\nbaz",
                'nested_inlined_multi_line_string' => [
                    'inlined_multi_line' => "foo\nbar\r\nempty line:\n\nbaz",
                ],
            ],
        ];

        $this->assertSame(file_get_contents(__DIR__.'/Fixtures/multiple_lines_as_literal_block.yml'), $this->dumper->dump($data, 2, 0, Yaml::DUMP_MULTI_LINE_LITERAL_BLOCK));
    }

    /**
     * @expectedException \InvalidArgumentException
     *
     * @expectedExceptionMessage The indentation must be greater than zero
     */
    public function test_zero_indentation_throws_exception()
    {
        new Dumper(0);
    }

    /**
     * @expectedException \InvalidArgumentException
     *
     * @expectedExceptionMessage The indentation must be greater than zero
     */
    public function test_negative_indentation_throws_exception()
    {
        new Dumper(-4);
    }
}

class A
{
    public $a = 'foo';
}
