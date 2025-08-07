<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\EventDispatcher\Tests\DependencyInjection;

use PHPUnit\Framework\TestCase;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\EventDispatcher\DependencyInjection\RegisterListenersPass;

class RegisterListenersPassTest extends TestCase
{
    /**
     * Tests that event subscribers not implementing EventSubscriberInterface
     * trigger an exception.
     *
     * @expectedException \InvalidArgumentException
     */
    public function test_event_subscriber_without_interface()
    {
        // one service, not implementing any interface
        $services = [
            'my_event_subscriber' => [0 => []],
        ];

        $definition = $this->getMockBuilder('Symfony\Component\DependencyInjection\Definition')->getMock();
        $definition->expects($this->atLeastOnce())
            ->method('isPublic')
            ->will($this->returnValue(true));
        $definition->expects($this->atLeastOnce())
            ->method('getClass')
            ->will($this->returnValue('stdClass'));

        $builder = $this->getMockBuilder('Symfony\Component\DependencyInjection\ContainerBuilder')->setMethods(['hasDefinition', 'findTaggedServiceIds', 'getDefinition'])->getMock();
        $builder->expects($this->any())
            ->method('hasDefinition')
            ->will($this->returnValue(true));

        // We don't test kernel.event_listener here
        $builder->expects($this->atLeastOnce())
            ->method('findTaggedServiceIds')
            ->will($this->onConsecutiveCalls([], $services));

        $builder->expects($this->atLeastOnce())
            ->method('getDefinition')
            ->will($this->returnValue($definition));

        $registerListenersPass = new RegisterListenersPass;
        $registerListenersPass->process($builder);
    }

    public function test_valid_event_subscriber()
    {
        $services = [
            'my_event_subscriber' => [0 => []],
        ];

        $definition = $this->getMockBuilder('Symfony\Component\DependencyInjection\Definition')->getMock();
        $definition->expects($this->atLeastOnce())
            ->method('isPublic')
            ->will($this->returnValue(true));
        $definition->expects($this->atLeastOnce())
            ->method('getClass')
            ->will($this->returnValue('Symfony\Component\EventDispatcher\Tests\DependencyInjection\SubscriberService'));

        $builder = $this->getMockBuilder('Symfony\Component\DependencyInjection\ContainerBuilder')->setMethods(['hasDefinition', 'findTaggedServiceIds', 'getDefinition', 'findDefinition'])->getMock();
        $builder->expects($this->any())
            ->method('hasDefinition')
            ->will($this->returnValue(true));

        // We don't test kernel.event_listener here
        $builder->expects($this->atLeastOnce())
            ->method('findTaggedServiceIds')
            ->will($this->onConsecutiveCalls([], $services));

        $builder->expects($this->atLeastOnce())
            ->method('getDefinition')
            ->will($this->returnValue($definition));

        $builder->expects($this->atLeastOnce())
            ->method('findDefinition')
            ->will($this->returnValue($definition));

        $registerListenersPass = new RegisterListenersPass;
        $registerListenersPass->process($builder);
    }

    /**
     * @expectedException \InvalidArgumentException
     *
     * @expectedExceptionMessage The service "foo" must be public as event listeners are lazy-loaded.
     */
    public function test_private_event_listener()
    {
        $container = new ContainerBuilder;
        $container->register('foo', 'stdClass')->setPublic(false)->addTag('kernel.event_listener', []);
        $container->register('event_dispatcher', 'stdClass');

        $registerListenersPass = new RegisterListenersPass;
        $registerListenersPass->process($container);
    }

    /**
     * @expectedException \InvalidArgumentException
     *
     * @expectedExceptionMessage The service "foo" must be public as event subscribers are lazy-loaded.
     */
    public function test_private_event_subscriber()
    {
        $container = new ContainerBuilder;
        $container->register('foo', 'stdClass')->setPublic(false)->addTag('kernel.event_subscriber', []);
        $container->register('event_dispatcher', 'stdClass');

        $registerListenersPass = new RegisterListenersPass;
        $registerListenersPass->process($container);
    }

    /**
     * @expectedException \InvalidArgumentException
     *
     * @expectedExceptionMessage The service "foo" must not be abstract as event listeners are lazy-loaded.
     */
    public function test_abstract_event_listener()
    {
        $container = new ContainerBuilder;
        $container->register('foo', 'stdClass')->setAbstract(true)->addTag('kernel.event_listener', []);
        $container->register('event_dispatcher', 'stdClass');

        $registerListenersPass = new RegisterListenersPass;
        $registerListenersPass->process($container);
    }

    /**
     * @expectedException \InvalidArgumentException
     *
     * @expectedExceptionMessage The service "foo" must not be abstract as event subscribers are lazy-loaded.
     */
    public function test_abstract_event_subscriber()
    {
        $container = new ContainerBuilder;
        $container->register('foo', 'stdClass')->setAbstract(true)->addTag('kernel.event_subscriber', []);
        $container->register('event_dispatcher', 'stdClass');

        $registerListenersPass = new RegisterListenersPass;
        $registerListenersPass->process($container);
    }

    public function test_event_subscriber_resolvable_class_name()
    {
        $container = new ContainerBuilder;

        $container->setParameter('subscriber.class', 'Symfony\Component\EventDispatcher\Tests\DependencyInjection\SubscriberService');
        $container->register('foo', '%subscriber.class%')->addTag('kernel.event_subscriber', []);
        $container->register('event_dispatcher', 'stdClass');

        $registerListenersPass = new RegisterListenersPass;
        $registerListenersPass->process($container);

        $definition = $container->getDefinition('event_dispatcher');
        $expected_calls = [
            [
                'addSubscriberService',
                [
                    'foo',
                    'Symfony\Component\EventDispatcher\Tests\DependencyInjection\SubscriberService',
                ],
            ],
        ];
        $this->assertSame($expected_calls, $definition->getMethodCalls());
    }

    /**
     * @expectedException \InvalidArgumentException
     *
     * @expectedExceptionMessage You have requested a non-existent parameter "subscriber.class"
     */
    public function test_event_subscriber_unresolvable_class_name()
    {
        $container = new ContainerBuilder;
        $container->register('foo', '%subscriber.class%')->addTag('kernel.event_subscriber', []);
        $container->register('event_dispatcher', 'stdClass');

        $registerListenersPass = new RegisterListenersPass;
        $registerListenersPass->process($container);
    }
}

class SubscriberService implements \Symfony\Component\EventDispatcher\EventSubscriberInterface
{
    public static function getSubscribedEvents() {}
}
