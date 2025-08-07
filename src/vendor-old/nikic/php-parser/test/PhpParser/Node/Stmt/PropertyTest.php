<?php

namespace PhpParser\Node\Stmt;

class PropertyTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @dataProvider provideModifiers
     */
    public function test_modifiers($modifier)
    {
        $node = new Property(
            constant('PhpParser\Node\Stmt\Class_::MODIFIER_'.strtoupper($modifier)),
            [] // invalid
        );

        $this->assertTrue($node->{'is'.$modifier}());
    }

    public function test_no_modifiers()
    {
        $node = new Property(0, []);

        $this->assertTrue($node->isPublic());
        $this->assertFalse($node->isProtected());
        $this->assertFalse($node->isPrivate());
        $this->assertFalse($node->isStatic());
    }

    public function test_static_implicitly_public()
    {
        $node = new Property(Class_::MODIFIER_STATIC, []);
        $this->assertTrue($node->isPublic());
        $this->assertFalse($node->isProtected());
        $this->assertFalse($node->isPrivate());
        $this->assertTrue($node->isStatic());
    }

    public function provideModifiers()
    {
        return [
            ['public'],
            ['protected'],
            ['private'],
            ['static'],
        ];
    }
}
