<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\HttpKernel\Tests\DependencyInjection;

use Symfony\Component\HttpKernel\DependencyInjection\MergeExtensionConfigurationPass;

class MergeExtensionConfigurationPassTest extends \PHPUnit_Framework_TestCase
{
    public function test_autoload_main_extension()
    {
        $container = $this->getMock(
            'Symfony\\Component\\DependencyInjection\\ContainerBuilder',
            [
                'getExtensionConfig',
                'loadFromExtension',
                'getParameterBag',
                'getDefinitions',
                'getAliases',
                'getExtensions',
            ]
        );
        $params = $this->getMock('Symfony\\Component\\DependencyInjection\\ParameterBag\\ParameterBag');

        $container->expects($this->at(0))
            ->method('getExtensionConfig')
            ->with('loaded')
            ->will($this->returnValue([[]]));
        $container->expects($this->at(1))
            ->method('getExtensionConfig')
            ->with('notloaded')
            ->will($this->returnValue([]));
        $container->expects($this->once())
            ->method('loadFromExtension')
            ->with('notloaded', []);

        $container->expects($this->any())
            ->method('getParameterBag')
            ->will($this->returnValue($params));
        $params->expects($this->any())
            ->method('all')
            ->will($this->returnValue([]));
        $container->expects($this->any())
            ->method('getDefinitions')
            ->will($this->returnValue([]));
        $container->expects($this->any())
            ->method('getAliases')
            ->will($this->returnValue([]));
        $container->expects($this->any())
            ->method('getExtensions')
            ->will($this->returnValue([]));

        $configPass = new MergeExtensionConfigurationPass(['loaded', 'notloaded']);
        $configPass->process($container);
    }
}
