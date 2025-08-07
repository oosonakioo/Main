<?php

namespace PhpParser;

use PhpParser\Node\Expr;

class BuilderFactoryTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @dataProvider provideTestFactory
     */
    public function test_factory($methodName, $className)
    {
        $factory = new BuilderFactory;
        $this->assertInstanceOf($className, $factory->$methodName('test'));
    }

    public function provideTestFactory()
    {
        return [
            ['namespace', 'PhpParser\Builder\Namespace_'],
            ['class',     'PhpParser\Builder\Class_'],
            ['interface', 'PhpParser\Builder\Interface_'],
            ['trait',     'PhpParser\Builder\Trait_'],
            ['method',    'PhpParser\Builder\Method'],
            ['function',  'PhpParser\Builder\Function_'],
            ['property',  'PhpParser\Builder\Property'],
            ['param',     'PhpParser\Builder\Param'],
            ['use',       'PhpParser\Builder\Use_'],
        ];
    }

    public function test_non_existing_method()
    {
        $this->setExpectedException('LogicException', 'Method "foo" does not exist');
        $factory = new BuilderFactory;
        $factory->foo();
    }

    public function test_integration()
    {
        $factory = new BuilderFactory;
        $node = $factory->namespace('Name\Space')
            ->addStmt($factory->use('Foo\Bar\SomeOtherClass'))
            ->addStmt($factory->use('Foo\Bar')->as('A'))
            ->addStmt($factory
                ->class('SomeClass')
                ->extend('SomeOtherClass')
                ->implement('A\Few', '\Interfaces')
                ->makeAbstract()

                ->addStmt($factory->method('firstMethod'))

                ->addStmt($factory->method('someMethod')
                    ->makePublic()
                    ->makeAbstract()
                    ->addParam($factory->param('someParam')->setTypeHint('SomeClass'))
                    ->setDocComment('/**
                                      * This method does something.
                                      *
                                      * @param SomeClass And takes a parameter
                                      */'))

                ->addStmt($factory->method('anotherMethod')
                    ->makeProtected()
                    ->addParam($factory->param('someParam')->setDefault('test'))
                    ->addStmt(new Expr\Print_(new Expr\Variable('someParam'))))

                ->addStmt($factory->property('someProperty')->makeProtected())
                ->addStmt($factory->property('anotherProperty')
                    ->makePrivate()
                    ->setDefault([1, 2, 3])))
            ->getNode();

        $expected = <<<'EOC'
<?php

namespace Name\Space;

use Foo\Bar\SomeOtherClass;
use Foo\Bar as A;
abstract class SomeClass extends SomeOtherClass implements A\Few, \Interfaces
{
    protected $someProperty;
    private $anotherProperty = array(1, 2, 3);
    function firstMethod()
    {
    }
    /**
     * This method does something.
     *
     * @param SomeClass And takes a parameter
     */
    public abstract function someMethod(SomeClass $someParam);
    protected function anotherMethod($someParam = 'test')
    {
        print $someParam;
    }
}
EOC;

        $stmts = [$node];
        $prettyPrinter = new PrettyPrinter\Standard;
        $generated = $prettyPrinter->prettyPrintFile($stmts);

        $this->assertEquals(
            str_replace("\r\n", "\n", $expected),
            str_replace("\r\n", "\n", $generated)
        );
    }
}
