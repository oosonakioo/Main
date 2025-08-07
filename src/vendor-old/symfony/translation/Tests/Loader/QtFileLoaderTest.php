<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\Translation\Tests\Loader;

use Symfony\Component\Config\Resource\FileResource;
use Symfony\Component\Translation\Loader\QtFileLoader;

class QtFileLoaderTest extends \PHPUnit_Framework_TestCase
{
    public function test_load()
    {
        $loader = new QtFileLoader;
        $resource = __DIR__.'/../fixtures/resources.ts';
        $catalogue = $loader->load($resource, 'en', 'resources');

        $this->assertEquals(['foo' => 'bar'], $catalogue->all('resources'));
        $this->assertEquals('en', $catalogue->getLocale());
        $this->assertEquals([new FileResource($resource)], $catalogue->getResources());
    }

    /**
     * @expectedException \Symfony\Component\Translation\Exception\NotFoundResourceException
     */
    public function test_load_non_existing_resource()
    {
        $loader = new QtFileLoader;
        $resource = __DIR__.'/../fixtures/non-existing.ts';
        $loader->load($resource, 'en', 'domain1');
    }

    /**
     * @expectedException \Symfony\Component\Translation\Exception\InvalidResourceException
     */
    public function test_load_non_local_resource()
    {
        $loader = new QtFileLoader;
        $resource = 'http://domain1.com/resources.ts';
        $loader->load($resource, 'en', 'domain1');
    }

    /**
     * @expectedException \Symfony\Component\Translation\Exception\InvalidResourceException
     */
    public function test_load_invalid_resource()
    {
        $loader = new QtFileLoader;
        $resource = __DIR__.'/../fixtures/invalid-xml-resources.xlf';
        $loader->load($resource, 'en', 'domain1');
    }

    public function test_load_empty_resource()
    {
        $loader = new QtFileLoader;
        $resource = __DIR__.'/../fixtures/empty.xlf';
        $this->setExpectedException('Symfony\Component\Translation\Exception\InvalidResourceException', sprintf('Unable to load "%s".', $resource));
        $loader->load($resource, 'en', 'domain1');
    }
}
