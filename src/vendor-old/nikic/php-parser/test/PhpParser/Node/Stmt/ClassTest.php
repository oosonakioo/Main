<?php

namespace PhpParser\Node\Stmt;

class ClassTest extends \PHPUnit_Framework_TestCase
{
    public function test_is_abstract()
    {
        $class = new Class_('Foo', ['type' => Class_::MODIFIER_ABSTRACT]);
        $this->assertTrue($class->isAbstract());

        $class = new Class_('Foo');
        $this->assertFalse($class->isAbstract());
    }

    public function test_is_final()
    {
        $class = new Class_('Foo', ['type' => Class_::MODIFIER_FINAL]);
        $this->assertTrue($class->isFinal());

        $class = new Class_('Foo');
        $this->assertFalse($class->isFinal());
    }

    public function test_get_methods()
    {
        $methods = [
            new ClassMethod('foo'),
            new ClassMethod('bar'),
            new ClassMethod('fooBar'),
        ];
        $class = new Class_('Foo', [
            'stmts' => [
                new TraitUse([]),
                $methods[0],
                new ClassConst([]),
                $methods[1],
                new Property(0, []),
                $methods[2],
            ],
        ]);

        $this->assertSame($methods, $class->getMethods());
    }

    public function test_get_method()
    {
        $methodConstruct = new ClassMethod('__CONSTRUCT');
        $methodTest = new ClassMethod('test');
        $class = new Class_('Foo', [
            'stmts' => [
                new ClassConst([]),
                $methodConstruct,
                new Property(0, []),
                $methodTest,
            ],
        ]);

        $this->assertSame($methodConstruct, $class->getMethod('__construct'));
        $this->assertSame($methodTest, $class->getMethod('test'));
        $this->assertNull($class->getMethod('nonExisting'));
    }
}
