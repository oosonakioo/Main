<?php

namespace PhpParser\Builder;

use PhpParser\Comment;
use PhpParser\Node;
use PhpParser\Node\Expr\Print_;
use PhpParser\Node\Scalar\String_;
use PhpParser\Node\Stmt;

class MethodTest extends \PHPUnit_Framework_TestCase
{
    public function createMethodBuilder($name)
    {
        return new Method($name);
    }

    public function test_modifiers()
    {
        $node = $this->createMethodBuilder('test')
            ->makePublic()
            ->makeAbstract()
            ->makeStatic()
            ->getNode();

        $this->assertEquals(
            new Stmt\ClassMethod('test', [
                'type' => Stmt\Class_::MODIFIER_PUBLIC
                        | Stmt\Class_::MODIFIER_ABSTRACT
                        | Stmt\Class_::MODIFIER_STATIC,
                'stmts' => null,
            ]),
            $node
        );

        $node = $this->createMethodBuilder('test')
            ->makeProtected()
            ->makeFinal()
            ->getNode();

        $this->assertEquals(
            new Stmt\ClassMethod('test', [
                'type' => Stmt\Class_::MODIFIER_PROTECTED
                        | Stmt\Class_::MODIFIER_FINAL,
            ]),
            $node
        );

        $node = $this->createMethodBuilder('test')
            ->makePrivate()
            ->getNode();

        $this->assertEquals(
            new Stmt\ClassMethod('test', [
                'type' => Stmt\Class_::MODIFIER_PRIVATE,
            ]),
            $node
        );
    }

    public function test_return_by_ref()
    {
        $node = $this->createMethodBuilder('test')
            ->makeReturnByRef()
            ->getNode();

        $this->assertEquals(
            new Stmt\ClassMethod('test', [
                'byRef' => true,
            ]),
            $node
        );
    }

    public function test_params()
    {
        $param1 = new Node\Param('test1');
        $param2 = new Node\Param('test2');
        $param3 = new Node\Param('test3');

        $node = $this->createMethodBuilder('test')
            ->addParam($param1)
            ->addParams([$param2, $param3])
            ->getNode();

        $this->assertEquals(
            new Stmt\ClassMethod('test', [
                'params' => [$param1, $param2, $param3],
            ]),
            $node
        );
    }

    public function test_stmts()
    {
        $stmt1 = new Print_(new String_('test1'));
        $stmt2 = new Print_(new String_('test2'));
        $stmt3 = new Print_(new String_('test3'));

        $node = $this->createMethodBuilder('test')
            ->addStmt($stmt1)
            ->addStmts([$stmt2, $stmt3])
            ->getNode();

        $this->assertEquals(
            new Stmt\ClassMethod('test', [
                'stmts' => [$stmt1, $stmt2, $stmt3],
            ]),
            $node
        );
    }

    public function test_doc_comment()
    {
        $node = $this->createMethodBuilder('test')
            ->setDocComment('/** Test */')
            ->getNode();

        $this->assertEquals(new Stmt\ClassMethod('test', [], [
            'comments' => [new Comment\Doc('/** Test */')],
        ]), $node);
    }

    public function test_return_type()
    {
        $node = $this->createMethodBuilder('test')
            ->setReturnType('bool')
            ->getNode();
        $this->assertEquals(new Stmt\ClassMethod('test', [
            'returnType' => 'bool',
        ], []), $node);
    }

    /**
     * @expectedException \LogicException
     *
     * @expectedExceptionMessage Cannot add statements to an abstract method
     */
    public function test_add_stmt_to_abstract_method_error()
    {
        $this->createMethodBuilder('test')
            ->makeAbstract()
            ->addStmt(new Print_(new String_('test')));
    }

    /**
     * @expectedException \LogicException
     *
     * @expectedExceptionMessage Cannot make method with statements abstract
     */
    public function test_make_method_with_stmts_abstract_error()
    {
        $this->createMethodBuilder('test')
            ->addStmt(new Print_(new String_('test')))
            ->makeAbstract();
    }

    /**
     * @expectedException \LogicException
     *
     * @expectedExceptionMessage Expected parameter node, got "Name"
     */
    public function test_invalid_param_error()
    {
        $this->createMethodBuilder('test')
            ->addParam(new Node\Name('foo'));
    }
}
