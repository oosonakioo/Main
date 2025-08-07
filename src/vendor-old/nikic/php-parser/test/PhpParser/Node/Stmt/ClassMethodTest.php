<?php

namespace PhpParser\Node\Stmt;

class ClassMethodTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @dataProvider provideModifiers
     */
    public function test_modifiers($modifier)
    {
        $node = new ClassMethod('foo', [
            'type' => constant('PhpParser\Node\Stmt\Class_::MODIFIER_'.strtoupper($modifier)),
        ]);

        $this->assertTrue($node->{'is'.$modifier}());
    }

    public function test_no_modifiers()
    {
        $node = new ClassMethod('foo', ['type' => 0]);

        $this->assertTrue($node->isPublic());
        $this->assertFalse($node->isProtected());
        $this->assertFalse($node->isPrivate());
        $this->assertFalse($node->isAbstract());
        $this->assertFalse($node->isFinal());
        $this->assertFalse($node->isStatic());
    }

    public function provideModifiers()
    {
        return [
            ['public'],
            ['protected'],
            ['private'],
            ['abstract'],
            ['final'],
            ['static'],
        ];
    }

    /**
     * Checks that implicit public modifier detection for method is working
     *
     * @dataProvider implicitPublicModifiers
     *
     * @param  int  $modifier  Node type modifier
     */
    public function test_implicit_public($modifier)
    {
        $node = new ClassMethod('foo', [
            'type' => constant('PhpParser\Node\Stmt\Class_::MODIFIER_'.strtoupper($modifier)),
        ]);

        $this->assertTrue($node->isPublic(), 'Node should be implicitly public');
    }

    public function implicitPublicModifiers()
    {
        return [
            ['abstract'],
            ['final'],
            ['static'],
        ];
    }
}
