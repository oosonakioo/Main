<?php

namespace PhpParser\Builder;

use PhpParser\Node;
use PhpParser\Node\Expr;
use PhpParser\Node\Scalar;

class ParamTest extends \PHPUnit_Framework_TestCase
{
    public function createParamBuilder($name)
    {
        return new Param($name);
    }

    /**
     * @dataProvider provideTestDefaultValues
     */
    public function test_default_values($value, $expectedValueNode)
    {
        $node = $this->createParamBuilder('test')
            ->setDefault($value)
            ->getNode();

        $this->assertEquals($expectedValueNode, $node->default);
    }

    public function provideTestDefaultValues()
    {
        return [
            [
                null,
                new Expr\ConstFetch(new Node\Name('null')),
            ],
            [
                true,
                new Expr\ConstFetch(new Node\Name('true')),
            ],
            [
                false,
                new Expr\ConstFetch(new Node\Name('false')),
            ],
            [
                31415,
                new Scalar\LNumber(31415),
            ],
            [
                3.1415,
                new Scalar\DNumber(3.1415),
            ],
            [
                'Hallo World',
                new Scalar\String_('Hallo World'),
            ],
            [
                [1, 2, 3],
                new Expr\Array_([
                    new Expr\ArrayItem(new Scalar\LNumber(1)),
                    new Expr\ArrayItem(new Scalar\LNumber(2)),
                    new Expr\ArrayItem(new Scalar\LNumber(3)),
                ]),
            ],
            [
                ['foo' => 'bar', 'bar' => 'foo'],
                new Expr\Array_([
                    new Expr\ArrayItem(
                        new Scalar\String_('bar'),
                        new Scalar\String_('foo')
                    ),
                    new Expr\ArrayItem(
                        new Scalar\String_('foo'),
                        new Scalar\String_('bar')
                    ),
                ]),
            ],
            [
                new Scalar\MagicConst\Dir,
                new Scalar\MagicConst\Dir,
            ],
        ];
    }

    public function test_type_hints()
    {
        $node = $this->createParamBuilder('test')
            ->setTypeHint('array')
            ->getNode();

        $this->assertEquals(
            new Node\Param('test', null, 'array'),
            $node
        );

        $node = $this->createParamBuilder('test')
            ->setTypeHint('callable')
            ->getNode();

        $this->assertEquals(
            new Node\Param('test', null, 'callable'),
            $node
        );

        $node = $this->createParamBuilder('test')
            ->setTypeHint('Some\Class')
            ->getNode();

        $this->assertEquals(
            new Node\Param('test', null, new Node\Name('Some\Class')),
            $node
        );
    }

    public function test_by_ref()
    {
        $node = $this->createParamBuilder('test')
            ->makeByRef()
            ->getNode();

        $this->assertEquals(
            new Node\Param('test', null, null, true),
            $node
        );
    }
}
