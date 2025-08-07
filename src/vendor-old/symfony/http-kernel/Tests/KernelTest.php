<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\HttpKernel\Tests;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Bundle\BundleInterface;
use Symfony\Component\HttpKernel\Config\EnvParametersResource;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\HttpKernel\Kernel;
use Symfony\Component\HttpKernel\Tests\Fixtures\KernelForOverrideName;
use Symfony\Component\HttpKernel\Tests\Fixtures\KernelForTest;

class KernelTest extends \PHPUnit_Framework_TestCase
{
    public function test_constructor()
    {
        $env = 'test_env';
        $debug = true;
        $kernel = new KernelForTest($env, $debug);

        $this->assertEquals($env, $kernel->getEnvironment());
        $this->assertEquals($debug, $kernel->isDebug());
        $this->assertFalse($kernel->isBooted());
        $this->assertLessThanOrEqual(microtime(true), $kernel->getStartTime());
        $this->assertNull($kernel->getContainer());
    }

    public function test_clone()
    {
        $env = 'test_env';
        $debug = true;
        $kernel = new KernelForTest($env, $debug);

        $clone = clone $kernel;

        $this->assertEquals($env, $clone->getEnvironment());
        $this->assertEquals($debug, $clone->isDebug());
        $this->assertFalse($clone->isBooted());
        $this->assertLessThanOrEqual(microtime(true), $clone->getStartTime());
        $this->assertNull($clone->getContainer());
    }

    public function test_boot_initializes_bundles_and_container()
    {
        $kernel = $this->getKernel(['initializeBundles', 'initializeContainer']);
        $kernel->expects($this->once())
            ->method('initializeBundles');
        $kernel->expects($this->once())
            ->method('initializeContainer');

        $kernel->boot();
    }

    public function test_boot_sets_the_container_to_the_bundles()
    {
        $bundle = $this->getMock('Symfony\Component\HttpKernel\Bundle\Bundle');
        $bundle->expects($this->once())
            ->method('setContainer');

        $kernel = $this->getKernel(['initializeBundles', 'initializeContainer', 'getBundles']);
        $kernel->expects($this->once())
            ->method('getBundles')
            ->will($this->returnValue([$bundle]));

        $kernel->boot();
    }

    public function test_boot_sets_the_booted_flag_to_true()
    {
        // use test kernel to access isBooted()
        $kernel = $this->getKernelForTest(['initializeBundles', 'initializeContainer']);
        $kernel->boot();

        $this->assertTrue($kernel->isBooted());
    }

    public function test_class_cache_is_loaded()
    {
        $kernel = $this->getKernel(['initializeBundles', 'initializeContainer', 'doLoadClassCache']);
        $kernel->loadClassCache('name', '.extension');
        $kernel->expects($this->once())
            ->method('doLoadClassCache')
            ->with('name', '.extension');

        $kernel->boot();
    }

    public function test_class_cache_is_not_loaded_by_default()
    {
        $kernel = $this->getKernel(['initializeBundles', 'initializeContainer', 'doLoadClassCache']);
        $kernel->expects($this->never())
            ->method('doLoadClassCache');

        $kernel->boot();
    }

    public function test_class_cache_is_not_loaded_when_kernel_is_not_booted()
    {
        $kernel = $this->getKernel(['initializeBundles', 'initializeContainer', 'doLoadClassCache']);
        $kernel->loadClassCache();
        $kernel->expects($this->never())
            ->method('doLoadClassCache');
    }

    public function test_env_parameters_resource_is_added()
    {
        $container = new ContainerBuilder;
        $kernel = $this->getMockBuilder('Symfony\Component\HttpKernel\Tests\Fixtures\KernelForTest')
            ->disableOriginalConstructor()
            ->setMethods(['getContainerBuilder', 'prepareContainer', 'getCacheDir', 'getLogDir'])
            ->getMock();
        $kernel->expects($this->any())
            ->method('getContainerBuilder')
            ->will($this->returnValue($container));
        $kernel->expects($this->any())
            ->method('prepareContainer')
            ->will($this->returnValue(null));
        $kernel->expects($this->any())
            ->method('getCacheDir')
            ->will($this->returnValue(sys_get_temp_dir()));
        $kernel->expects($this->any())
            ->method('getLogDir')
            ->will($this->returnValue(sys_get_temp_dir()));

        $reflection = new \ReflectionClass(get_class($kernel));
        $method = $reflection->getMethod('buildContainer');
        $method->setAccessible(true);
        $method->invoke($kernel);

        $found = false;
        foreach ($container->getResources() as $resource) {
            if ($resource instanceof EnvParametersResource) {
                $found = true;
                break;
            }
        }

        $this->assertTrue($found);
    }

    public function test_boot_kernel_several_times_only_initializes_bundles_once()
    {
        $kernel = $this->getKernel(['initializeBundles', 'initializeContainer']);
        $kernel->expects($this->once())
            ->method('initializeBundles');

        $kernel->boot();
        $kernel->boot();
    }

    public function test_shutdown_calls_shutdown_on_all_bundles()
    {
        $bundle = $this->getMock('Symfony\Component\HttpKernel\Bundle\Bundle');
        $bundle->expects($this->once())
            ->method('shutdown');

        $kernel = $this->getKernel([], [$bundle]);

        $kernel->boot();
        $kernel->shutdown();
    }

    public function test_shutdown_gives_null_container_to_all_bundles()
    {
        $bundle = $this->getMock('Symfony\Component\HttpKernel\Bundle\Bundle');
        $bundle->expects($this->at(3))
            ->method('setContainer')
            ->with(null);

        $kernel = $this->getKernel(['getBundles']);
        $kernel->expects($this->any())
            ->method('getBundles')
            ->will($this->returnValue([$bundle]));

        $kernel->boot();
        $kernel->shutdown();
    }

    public function test_handle_calls_handle_on_http_kernel()
    {
        $type = HttpKernelInterface::MASTER_REQUEST;
        $catch = true;
        $request = new Request;

        $httpKernelMock = $this->getMockBuilder('Symfony\Component\HttpKernel\HttpKernel')
            ->disableOriginalConstructor()
            ->getMock();
        $httpKernelMock
            ->expects($this->once())
            ->method('handle')
            ->with($request, $type, $catch);

        $kernel = $this->getKernel(['getHttpKernel']);
        $kernel->expects($this->once())
            ->method('getHttpKernel')
            ->will($this->returnValue($httpKernelMock));

        $kernel->handle($request, $type, $catch);
    }

    public function test_handle_boots_the_kernel()
    {
        $type = HttpKernelInterface::MASTER_REQUEST;
        $catch = true;
        $request = new Request;

        $httpKernelMock = $this->getMockBuilder('Symfony\Component\HttpKernel\HttpKernel')
            ->disableOriginalConstructor()
            ->getMock();

        $kernel = $this->getKernel(['getHttpKernel', 'boot']);
        $kernel->expects($this->once())
            ->method('getHttpKernel')
            ->will($this->returnValue($httpKernelMock));

        $kernel->expects($this->once())
            ->method('boot');

        $kernel->handle($request, $type, $catch);
    }

    public function test_strip_comments()
    {
        $source = <<<'EOF'
<?php

$string = 'string should not be   modified';

$string = 'string should not be

modified';


$heredoc = <<<HD


Heredoc should not be   modified {$a[1+$b]}


HD;

$nowdoc = <<<'ND'


Nowdoc should not be   modified


ND;

/**
 * some class comments to strip
 */
class TestClass
{
    /**
     * some method comments to strip
     */
    public function doStuff()
    {
        // inline comment
    }
}
EOF;
        $expected = <<<'EOF'
<?php
$string = 'string should not be   modified';
$string = 'string should not be

modified';
$heredoc = <<<HD


Heredoc should not be   modified {$a[1+$b]}


HD;
$nowdoc = <<<'ND'


Nowdoc should not be   modified


ND;
class TestClass
{
    public function doStuff()
    {
        }
}
EOF;

        $output = Kernel::stripComments($source);

        // Heredocs are preserved, making the output mixing Unix and Windows line
        // endings, switching to "\n" everywhere on Windows to avoid failure.
        if ('\\' === DIRECTORY_SEPARATOR) {
            $expected = str_replace("\r\n", "\n", $expected);
            $output = str_replace("\r\n", "\n", $output);
        }

        $this->assertEquals($expected, $output);
    }

    public function test_get_root_dir()
    {
        $kernel = new KernelForTest('test', true);

        $this->assertEquals(__DIR__.DIRECTORY_SEPARATOR.'Fixtures', realpath($kernel->getRootDir()));
    }

    public function test_get_name()
    {
        $kernel = new KernelForTest('test', true);

        $this->assertEquals('Fixtures', $kernel->getName());
    }

    public function test_override_get_name()
    {
        $kernel = new KernelForOverrideName('test', true);

        $this->assertEquals('overridden', $kernel->getName());
    }

    public function test_serialize()
    {
        $env = 'test_env';
        $debug = true;
        $kernel = new KernelForTest($env, $debug);

        $expected = serialize([$env, $debug]);
        $this->assertEquals($expected, $kernel->serialize());
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function test_locate_resource_throws_exception_when_name_is_not_valid()
    {
        $this->getKernel()->locateResource('Foo');
    }

    /**
     * @expectedException \RuntimeException
     */
    public function test_locate_resource_throws_exception_when_name_is_unsafe()
    {
        $this->getKernel()->locateResource('@FooBundle/../bar');
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function test_locate_resource_throws_exception_when_bundle_does_not_exist()
    {
        $this->getKernel()->locateResource('@FooBundle/config/routing.xml');
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function test_locate_resource_throws_exception_when_resource_does_not_exist()
    {
        $kernel = $this->getKernel(['getBundle']);
        $kernel
            ->expects($this->once())
            ->method('getBundle')
            ->will($this->returnValue([$this->getBundle(__DIR__.'/Fixtures/Bundle1Bundle')]));

        $kernel->locateResource('@Bundle1Bundle/config/routing.xml');
    }

    public function test_locate_resource_returns_the_first_that_matches()
    {
        $kernel = $this->getKernel(['getBundle']);
        $kernel
            ->expects($this->once())
            ->method('getBundle')
            ->will($this->returnValue([$this->getBundle(__DIR__.'/Fixtures/Bundle1Bundle')]));

        $this->assertEquals(__DIR__.'/Fixtures/Bundle1Bundle/foo.txt', $kernel->locateResource('@Bundle1Bundle/foo.txt'));
    }

    public function test_locate_resource_returns_the_first_that_matches_with_parent()
    {
        $parent = $this->getBundle(__DIR__.'/Fixtures/Bundle1Bundle');
        $child = $this->getBundle(__DIR__.'/Fixtures/Bundle2Bundle');

        $kernel = $this->getKernel(['getBundle']);
        $kernel
            ->expects($this->exactly(2))
            ->method('getBundle')
            ->will($this->returnValue([$child, $parent]));

        $this->assertEquals(__DIR__.'/Fixtures/Bundle2Bundle/foo.txt', $kernel->locateResource('@ParentAABundle/foo.txt'));
        $this->assertEquals(__DIR__.'/Fixtures/Bundle1Bundle/bar.txt', $kernel->locateResource('@ParentAABundle/bar.txt'));
    }

    public function test_locate_resource_returns_all_matches()
    {
        $parent = $this->getBundle(__DIR__.'/Fixtures/Bundle1Bundle');
        $child = $this->getBundle(__DIR__.'/Fixtures/Bundle2Bundle');

        $kernel = $this->getKernel(['getBundle']);
        $kernel
            ->expects($this->once())
            ->method('getBundle')
            ->will($this->returnValue([$child, $parent]));

        $this->assertEquals([
            __DIR__.'/Fixtures/Bundle2Bundle/foo.txt',
            __DIR__.'/Fixtures/Bundle1Bundle/foo.txt', ],
            $kernel->locateResource('@Bundle1Bundle/foo.txt', null, false));
    }

    public function test_locate_resource_returns_all_matches_bis()
    {
        $kernel = $this->getKernel(['getBundle']);
        $kernel
            ->expects($this->once())
            ->method('getBundle')
            ->will($this->returnValue([
                $this->getBundle(__DIR__.'/Fixtures/Bundle1Bundle'),
                $this->getBundle(__DIR__.'/Foobar'),
            ]));

        $this->assertEquals(
            [__DIR__.'/Fixtures/Bundle1Bundle/foo.txt'],
            $kernel->locateResource('@Bundle1Bundle/foo.txt', null, false)
        );
    }

    public function test_locate_resource_ignores_dir_on_non_resource()
    {
        $kernel = $this->getKernel(['getBundle']);
        $kernel
            ->expects($this->once())
            ->method('getBundle')
            ->will($this->returnValue([$this->getBundle(__DIR__.'/Fixtures/Bundle1Bundle')]));

        $this->assertEquals(
            __DIR__.'/Fixtures/Bundle1Bundle/foo.txt',
            $kernel->locateResource('@Bundle1Bundle/foo.txt', __DIR__.'/Fixtures')
        );
    }

    public function test_locate_resource_returns_the_dir_one_for_resources()
    {
        $kernel = $this->getKernel(['getBundle']);
        $kernel
            ->expects($this->once())
            ->method('getBundle')
            ->will($this->returnValue([$this->getBundle(__DIR__.'/Fixtures/FooBundle', null, null, 'FooBundle')]));

        $this->assertEquals(
            __DIR__.'/Fixtures/Resources/FooBundle/foo.txt',
            $kernel->locateResource('@FooBundle/Resources/foo.txt', __DIR__.'/Fixtures/Resources')
        );
    }

    public function test_locate_resource_returns_the_dir_one_for_resources_and_bundle_ones()
    {
        $kernel = $this->getKernel(['getBundle']);
        $kernel
            ->expects($this->once())
            ->method('getBundle')
            ->will($this->returnValue([$this->getBundle(__DIR__.'/Fixtures/Bundle1Bundle', null, null, 'Bundle1Bundle')]));

        $this->assertEquals([
            __DIR__.'/Fixtures/Resources/Bundle1Bundle/foo.txt',
            __DIR__.'/Fixtures/Bundle1Bundle/Resources/foo.txt', ],
            $kernel->locateResource('@Bundle1Bundle/Resources/foo.txt', __DIR__.'/Fixtures/Resources', false)
        );
    }

    public function test_locate_resource_override_bundle_and_resources_folders()
    {
        $parent = $this->getBundle(__DIR__.'/Fixtures/BaseBundle', null, 'BaseBundle', 'BaseBundle');
        $child = $this->getBundle(__DIR__.'/Fixtures/ChildBundle', 'ParentBundle', 'ChildBundle', 'ChildBundle');

        $kernel = $this->getKernel(['getBundle']);
        $kernel
            ->expects($this->exactly(4))
            ->method('getBundle')
            ->will($this->returnValue([$child, $parent]));

        $this->assertEquals([
            __DIR__.'/Fixtures/Resources/ChildBundle/foo.txt',
            __DIR__.'/Fixtures/ChildBundle/Resources/foo.txt',
            __DIR__.'/Fixtures/BaseBundle/Resources/foo.txt',
        ],
            $kernel->locateResource('@BaseBundle/Resources/foo.txt', __DIR__.'/Fixtures/Resources', false)
        );

        $this->assertEquals(
            __DIR__.'/Fixtures/Resources/ChildBundle/foo.txt',
            $kernel->locateResource('@BaseBundle/Resources/foo.txt', __DIR__.'/Fixtures/Resources')
        );

        try {
            $kernel->locateResource('@BaseBundle/Resources/hide.txt', __DIR__.'/Fixtures/Resources', false);
            $this->fail('Hidden resources should raise an exception when returning an array of matching paths');
        } catch (\RuntimeException $e) {
        }

        try {
            $kernel->locateResource('@BaseBundle/Resources/hide.txt', __DIR__.'/Fixtures/Resources', true);
            $this->fail('Hidden resources should raise an exception when returning the first matching path');
        } catch (\RuntimeException $e) {
        }
    }

    public function test_locate_resource_on_directories()
    {
        $kernel = $this->getKernel(['getBundle']);
        $kernel
            ->expects($this->exactly(2))
            ->method('getBundle')
            ->will($this->returnValue([$this->getBundle(__DIR__.'/Fixtures/FooBundle', null, null, 'FooBundle')]));

        $this->assertEquals(
            __DIR__.'/Fixtures/Resources/FooBundle/',
            $kernel->locateResource('@FooBundle/Resources/', __DIR__.'/Fixtures/Resources')
        );
        $this->assertEquals(
            __DIR__.'/Fixtures/Resources/FooBundle',
            $kernel->locateResource('@FooBundle/Resources', __DIR__.'/Fixtures/Resources')
        );

        $kernel = $this->getKernel(['getBundle']);
        $kernel
            ->expects($this->exactly(2))
            ->method('getBundle')
            ->will($this->returnValue([$this->getBundle(__DIR__.'/Fixtures/Bundle1Bundle', null, null, 'Bundle1Bundle')]));

        $this->assertEquals(
            __DIR__.'/Fixtures/Bundle1Bundle/Resources/',
            $kernel->locateResource('@Bundle1Bundle/Resources/')
        );
        $this->assertEquals(
            __DIR__.'/Fixtures/Bundle1Bundle/Resources',
            $kernel->locateResource('@Bundle1Bundle/Resources')
        );
    }

    public function test_initialize_bundles()
    {
        $parent = $this->getBundle(null, null, 'ParentABundle');
        $child = $this->getBundle(null, 'ParentABundle', 'ChildABundle');

        // use test kernel so we can access getBundleMap()
        $kernel = $this->getKernelForTest(['registerBundles']);
        $kernel
            ->expects($this->once())
            ->method('registerBundles')
            ->will($this->returnValue([$parent, $child]));
        $kernel->boot();

        $map = $kernel->getBundleMap();
        $this->assertEquals([$child, $parent], $map['ParentABundle']);
    }

    public function test_initialize_bundles_support_inheritance_cascade()
    {
        $grandparent = $this->getBundle(null, null, 'GrandParentBBundle');
        $parent = $this->getBundle(null, 'GrandParentBBundle', 'ParentBBundle');
        $child = $this->getBundle(null, 'ParentBBundle', 'ChildBBundle');

        // use test kernel so we can access getBundleMap()
        $kernel = $this->getKernelForTest(['registerBundles']);
        $kernel
            ->expects($this->once())
            ->method('registerBundles')
            ->will($this->returnValue([$grandparent, $parent, $child]));
        $kernel->boot();

        $map = $kernel->getBundleMap();
        $this->assertEquals([$child, $parent, $grandparent], $map['GrandParentBBundle']);
        $this->assertEquals([$child, $parent], $map['ParentBBundle']);
        $this->assertEquals([$child], $map['ChildBBundle']);
    }

    /**
     * @expectedException \LogicException
     *
     * @expectedExceptionMessage Bundle "ChildCBundle" extends bundle "FooBar", which is not registered.
     */
    public function test_initialize_bundles_throws_exception_when_a_parent_does_not_exists()
    {
        $child = $this->getBundle(null, 'FooBar', 'ChildCBundle');
        $kernel = $this->getKernel([], [$child]);
        $kernel->boot();
    }

    public function test_initialize_bundles_supports_arbitrary_bundle_registration_order()
    {
        $grandparent = $this->getBundle(null, null, 'GrandParentCBundle');
        $parent = $this->getBundle(null, 'GrandParentCBundle', 'ParentCBundle');
        $child = $this->getBundle(null, 'ParentCBundle', 'ChildCBundle');

        // use test kernel so we can access getBundleMap()
        $kernel = $this->getKernelForTest(['registerBundles']);
        $kernel
            ->expects($this->once())
            ->method('registerBundles')
            ->will($this->returnValue([$parent, $grandparent, $child]));
        $kernel->boot();

        $map = $kernel->getBundleMap();
        $this->assertEquals([$child, $parent, $grandparent], $map['GrandParentCBundle']);
        $this->assertEquals([$child, $parent], $map['ParentCBundle']);
        $this->assertEquals([$child], $map['ChildCBundle']);
    }

    /**
     * @expectedException \LogicException
     *
     * @expectedExceptionMessage Bundle "ParentCBundle" is directly extended by two bundles "ChildC2Bundle" and "ChildC1Bundle".
     */
    public function test_initialize_bundles_throws_exception_when_a_bundle_is_directly_extended_by_two_bundles()
    {
        $parent = $this->getBundle(null, null, 'ParentCBundle');
        $child1 = $this->getBundle(null, 'ParentCBundle', 'ChildC1Bundle');
        $child2 = $this->getBundle(null, 'ParentCBundle', 'ChildC2Bundle');

        $kernel = $this->getKernel([], [$parent, $child1, $child2]);
        $kernel->boot();
    }

    /**
     * @expectedException \LogicException
     *
     * @expectedExceptionMessage Trying to register two bundles with the same name "DuplicateName"
     */
    public function test_initialize_bundle_throws_exception_when_registering_two_bundles_with_the_same_name()
    {
        $fooBundle = $this->getBundle(null, null, 'FooBundle', 'DuplicateName');
        $barBundle = $this->getBundle(null, null, 'BarBundle', 'DuplicateName');

        $kernel = $this->getKernel([], [$fooBundle, $barBundle]);
        $kernel->boot();
    }

    /**
     * @expectedException \LogicException
     *
     * @expectedExceptionMessage Bundle "CircularRefBundle" can not extend itself.
     */
    public function test_initialize_bundle_throws_exception_when_a_bundle_extends_itself()
    {
        $circularRef = $this->getBundle(null, 'CircularRefBundle', 'CircularRefBundle');

        $kernel = $this->getKernel([], [$circularRef]);
        $kernel->boot();
    }

    public function test_terminate_returns_silently_if_kernel_is_not_booted()
    {
        $kernel = $this->getKernel(['getHttpKernel']);
        $kernel->expects($this->never())
            ->method('getHttpKernel');

        $kernel->terminate(Request::create('/'), new Response);
    }

    public function test_terminate_delegates_termination_only_for_terminable_interface()
    {
        // does not implement TerminableInterface
        $httpKernel = new TestKernel;

        $kernel = $this->getKernel(['getHttpKernel']);
        $kernel->expects($this->once())
            ->method('getHttpKernel')
            ->willReturn($httpKernel);

        $kernel->boot();
        $kernel->terminate(Request::create('/'), new Response);

        $this->assertFalse($httpKernel->terminateCalled, 'terminate() is never called if the kernel class does not implement TerminableInterface');

        // implements TerminableInterface
        $httpKernelMock = $this->getMockBuilder('Symfony\Component\HttpKernel\HttpKernel')
            ->disableOriginalConstructor()
            ->setMethods(['terminate'])
            ->getMock();

        $httpKernelMock
            ->expects($this->once())
            ->method('terminate');

        $kernel = $this->getKernel(['getHttpKernel']);
        $kernel->expects($this->exactly(2))
            ->method('getHttpKernel')
            ->will($this->returnValue($httpKernelMock));

        $kernel->boot();
        $kernel->terminate(Request::create('/'), new Response);
    }

    /**
     * Returns a mock for the BundleInterface.
     *
     * @return BundleInterface
     */
    protected function getBundle($dir = null, $parent = null, $className = null, $bundleName = null)
    {
        $bundle = $this
            ->getMockBuilder('Symfony\Component\HttpKernel\Bundle\BundleInterface')
            ->setMethods(['getPath', 'getParent', 'getName'])
            ->disableOriginalConstructor();

        if ($className) {
            $bundle->setMockClassName($className);
        }

        $bundle = $bundle->getMockForAbstractClass();

        $bundle
            ->expects($this->any())
            ->method('getName')
            ->will($this->returnValue($bundleName === null ? get_class($bundle) : $bundleName));

        $bundle
            ->expects($this->any())
            ->method('getPath')
            ->will($this->returnValue($dir));

        $bundle
            ->expects($this->any())
            ->method('getParent')
            ->will($this->returnValue($parent));

        return $bundle;
    }

    /**
     * Returns a mock for the abstract kernel.
     *
     * @param  array  $methods  Additional methods to mock (besides the abstract ones)
     * @param  array  $bundles  Bundles to register
     * @return Kernel
     */
    protected function getKernel(array $methods = [], array $bundles = [])
    {
        $methods[] = 'registerBundles';

        $kernel = $this
            ->getMockBuilder('Symfony\Component\HttpKernel\Kernel')
            ->setMethods($methods)
            ->setConstructorArgs(['test', false])
            ->getMockForAbstractClass();
        $kernel->expects($this->any())
            ->method('registerBundles')
            ->will($this->returnValue($bundles));
        $p = new \ReflectionProperty($kernel, 'rootDir');
        $p->setAccessible(true);
        $p->setValue($kernel, __DIR__.'/Fixtures');

        return $kernel;
    }

    protected function getKernelForTest(array $methods = [])
    {
        $kernel = $this->getMockBuilder('Symfony\Component\HttpKernel\Tests\Fixtures\KernelForTest')
            ->setConstructorArgs(['test', false])
            ->setMethods($methods)
            ->getMock();
        $p = new \ReflectionProperty($kernel, 'rootDir');
        $p->setAccessible(true);
        $p->setValue($kernel, __DIR__.'/Fixtures');

        return $kernel;
    }
}

class TestKernel implements HttpKernelInterface
{
    public $terminateCalled = false;

    public function terminate()
    {
        $this->terminateCalled = true;
    }

    public function handle(Request $request, $type = self::MASTER_REQUEST, $catch = true) {}
}
