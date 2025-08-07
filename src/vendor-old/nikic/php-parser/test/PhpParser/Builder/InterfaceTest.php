<?php

namespace PhpParser\Builder;

use PhpParser\Comment;
use PhpParser\Node;
use PhpParser\Node\Scalar\DNumber;
use PhpParser\Node\Stmt;

class InterfaceTest extends \PHPUnit_Framework_TestCase
{
    /** @var Interface_ */
    protected $builder;

    protected function setUp()
    {
        $this->builder = new Interface_('Contract');
    }

    private function dump($node)
    {
        $pp = new \PhpParser\PrettyPrinter\Standard;

        return $pp->prettyPrint([$node]);
    }

    public function test_empty()
    {
        $contract = $this->builder->getNode();
        $this->assertInstanceOf('PhpParser\Node\Stmt\Interface_', $contract);
        $this->assertSame('Contract', $contract->name);
    }

    public function test_extending()
    {
        $contract = $this->builder->extend('Space\Root1', 'Root2')->getNode();
        $this->assertEquals(
            new Stmt\Interface_('Contract', [
                'extends' => [
                    new Node\Name('Space\Root1'),
                    new Node\Name('Root2'),
                ],
            ]), $contract
        );
    }

    public function test_add_method()
    {
        $method = new Stmt\ClassMethod('doSomething');
        $contract = $this->builder->addStmt($method)->getNode();
        $this->assertSame([$method], $contract->stmts);
    }

    public function test_add_const()
    {
        $const = new Stmt\ClassConst([
            new Node\Const_('SPEED_OF_LIGHT', new DNumber(299792458.0)),
        ]);
        $contract = $this->builder->addStmt($const)->getNode();
        $this->assertSame(299792458.0, $contract->stmts[0]->consts[0]->value->value);
    }

    public function test_order()
    {
        $const = new Stmt\ClassConst([
            new Node\Const_('SPEED_OF_LIGHT', new DNumber(299792458)),
        ]);
        $method = new Stmt\ClassMethod('doSomething');
        $contract = $this->builder
            ->addStmt($method)
            ->addStmt($const)
            ->getNode();

        $this->assertInstanceOf('PhpParser\Node\Stmt\ClassConst', $contract->stmts[0]);
        $this->assertInstanceOf('PhpParser\Node\Stmt\ClassMethod', $contract->stmts[1]);
    }

    public function test_doc_comment()
    {
        $node = $this->builder
            ->setDocComment('/** Test */')
            ->getNode();

        $this->assertEquals(new Stmt\Interface_('Contract', [], [
            'comments' => [new Comment\Doc('/** Test */')],
        ]), $node);
    }

    /**
     * @expectedException \LogicException
     *
     * @expectedExceptionMessage Unexpected node of type "Stmt_PropertyProperty"
     */
    public function test_invalid_stmt_error()
    {
        $this->builder->addStmt(new Stmt\PropertyProperty('invalid'));
    }

    public function test_full_functional()
    {
        $const = new Stmt\ClassConst([
            new Node\Const_('SPEED_OF_LIGHT', new DNumber(299792458)),
        ]);
        $method = new Stmt\ClassMethod('doSomething');
        $contract = $this->builder
            ->addStmt($method)
            ->addStmt($const)
            ->getNode();

        eval($this->dump($contract));

        $this->assertTrue(interface_exists('Contract', false));
    }
}
