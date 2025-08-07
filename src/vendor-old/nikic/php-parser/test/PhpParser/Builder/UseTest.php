<?php

use PhpParser\Builder;
use PhpParser\Node\Name;
use PhpParser\Node\Stmt;

class UseTest extends \PHPUnit_Framework_TestCase
{
    protected function createUseBuilder($name, $type = Stmt\Use_::TYPE_NORMAL)
    {
        return new Builder\Use_($name, $type);
    }

    public function test_creation()
    {
        $node = $this->createUseBuilder('Foo\Bar')->getNode();
        $this->assertEquals(new Stmt\Use_([
            new Stmt\UseUse(new Name('Foo\Bar'), 'Bar'),
        ]), $node);

        $node = $this->createUseBuilder(new Name('Foo\Bar'))->as('XYZ')->getNode();
        $this->assertEquals(new Stmt\Use_([
            new Stmt\UseUse(new Name('Foo\Bar'), 'XYZ'),
        ]), $node);

        $node = $this->createUseBuilder('foo\bar', Stmt\Use_::TYPE_FUNCTION)->as('foo')->getNode();
        $this->assertEquals(new Stmt\Use_([
            new Stmt\UseUse(new Name('foo\bar'), 'foo'),
        ], Stmt\Use_::TYPE_FUNCTION), $node);
    }

    public function test_non_existing_method()
    {
        $this->setExpectedException('LogicException', 'Method "foo" does not exist');
        $builder = $this->createUseBuilder('Test');
        $builder->foo();
    }
}
