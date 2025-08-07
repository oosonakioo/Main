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
use Symfony\Component\Translation\Loader\MoFileLoader;

class MoFileLoaderTest extends \PHPUnit_Framework_TestCase
{
    public function test_load()
    {
        $loader = new MoFileLoader;
        $resource = __DIR__.'/../fixtures/resources.mo';
        $catalogue = $loader->load($resource, 'en', 'domain1');

        $this->assertEquals(['foo' => 'bar'], $catalogue->all('domain1'));
        $this->assertEquals('en', $catalogue->getLocale());
        $this->assertEquals([new FileResource($resource)], $catalogue->getResources());
    }

    public function test_load_plurals()
    {
        $loader = new MoFileLoader;
        $resource = __DIR__.'/../fixtures/plurals.mo';
        $catalogue = $loader->load($resource, 'en', 'domain1');

        $this->assertEquals(['foo' => 'bar', 'foos' => '{0} bar|{1} bars'], $catalogue->all('domain1'));
        $this->assertEquals('en', $catalogue->getLocale());
        $this->assertEquals([new FileResource($resource)], $catalogue->getResources());
    }

    /**
     * @expectedException \Symfony\Component\Translation\Exception\NotFoundResourceException
     */
    public function test_load_non_existing_resource()
    {
        $loader = new MoFileLoader;
        $resource = __DIR__.'/../fixtures/non-existing.mo';
        $loader->load($resource, 'en', 'domain1');
    }

    /**
     * @expectedException \Symfony\Component\Translation\Exception\InvalidResourceException
     */
    public function test_load_invalid_resource()
    {
        $loader = new MoFileLoader;
        $resource = __DIR__.'/../fixtures/empty.mo';
        $loader->load($resource, 'en', 'domain1');
    }

    public function test_load_empty_translation()
    {
        $loader = new MoFileLoader;
        $resource = __DIR__.'/../fixtures/empty-translation.mo';
        $catalogue = $loader->load($resource, 'en', 'message');

        $this->assertEquals([], $catalogue->all('message'));
        $this->assertEquals('en', $catalogue->getLocale());
        $this->assertEquals([new FileResource($resource)], $catalogue->getResources());
    }
}
