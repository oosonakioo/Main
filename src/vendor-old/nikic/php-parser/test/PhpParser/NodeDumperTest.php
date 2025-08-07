<?php

namespace PhpParser;

class NodeDumperTest extends \PHPUnit_Framework_TestCase
{
    private function canonicalize($string)
    {
        return str_replace("\r\n", "\n", $string);
    }

    /**
     * @dataProvider provideTestDump
     *
     * @covers PhpParser\NodeDumper::dump
     */
    public function test_dump($node, $dump)
    {
        $dumper = new NodeDumper;

        $this->assertSame($this->canonicalize($dump), $this->canonicalize($dumper->dump($node)));
    }

    public function provideTestDump()
    {
        return [
            [
                [],
                'array(
)',
            ],
            [
                ['Foo', 'Bar', 'Key' => 'FooBar'],
                'array(
    0: Foo
    1: Bar
    Key: FooBar
)',
            ],
            [
                new Node\Name(['Hallo', 'World']),
                'Name(
    parts: array(
        0: Hallo
        1: World
    )
)',
            ],
            [
                new Node\Expr\Array_([
                    new Node\Expr\ArrayItem(new Node\Scalar\String_('Foo')),
                ]),
                'Expr_Array(
    items: array(
        0: Expr_ArrayItem(
            key: null
            value: Scalar_String(
                value: Foo
            )
            byRef: false
        )
    )
)',
            ],
        ];
    }

    /**
     * @expectedException        \InvalidArgumentException
     *
     * @expectedExceptionMessage Can only dump nodes and arrays.
     */
    public function test_error()
    {
        $dumper = new NodeDumper;
        $dumper->dump(new \stdClass);
    }
}
