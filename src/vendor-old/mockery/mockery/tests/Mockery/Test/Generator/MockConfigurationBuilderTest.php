<?php

namespace Mockery\Generator;

class MockConfigurationBuilderTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function reserved_words_are_black_listed_by_default()
    {
        $builder = new MockConfigurationBuilder;
        $this->assertContains('abstract', $builder->getMockConfiguration()->getBlackListedMethods());

        // need a builtin for this
        $this->markTestSkipped('Need a builtin class with a method that is a reserved word');
    }

    /**
     * @test
     */
    public function magic_methods_are_black_listed_by_default()
    {
        $builder = new MockConfigurationBuilder;
        $builder->addTarget("Mockery\Generator\ClassWithMagicCall");
        $methods = $builder->getMockConfiguration()->getMethodsToMock();
        $this->assertEquals(1, count($methods));
        $this->assertEquals('foo', $methods[0]->getName());
    }
}

class ClassWithMagicCall
{
    public function foo() {}

    public function __call($method, $args) {}
}
