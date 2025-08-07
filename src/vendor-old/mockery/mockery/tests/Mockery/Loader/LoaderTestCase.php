<?php

namespace Mockery\Loader;

use Mockery\Generator\MockConfiguration;
use Mockery\Generator\MockDefinition;

abstract class LoaderTestCase extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function load_loads_the_code()
    {
        $className = 'Mock_'.uniqid();
        $config = new MockConfiguration([], [], [], $className);
        $code = "<?php class $className { } ";

        $definition = new MockDefinition($config, $code);

        $this->getLoader()->load($definition);

        $this->assertTrue(class_exists($className));
    }

    abstract public function getLoader();
}
