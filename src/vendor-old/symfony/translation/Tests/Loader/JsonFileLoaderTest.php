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
use Symfony\Component\Translation\Loader\JsonFileLoader;

class JsonFileLoaderTest extends \PHPUnit_Framework_TestCase
{
    public function test_load()
    {
        $loader = new JsonFileLoader;
        $resource = __DIR__.'/../fixtures/resources.json';
        $catalogue = $loader->load($resource, 'en', 'domain1');

        $this->assertEquals(['foo' => 'bar'], $catalogue->all('domain1'));
        $this->assertEquals('en', $catalogue->getLocale());
        $this->assertEquals([new FileResource($resource)], $catalogue->getResources());
    }

    public function test_load_does_nothing_if_empty()
    {
        $loader = new JsonFileLoader;
        $resource = __DIR__.'/../fixtures/empty.json';
        $catalogue = $loader->load($resource, 'en', 'domain1');

        $this->assertEquals([], $catalogue->all('domain1'));
        $this->assertEquals('en', $catalogue->getLocale());
        $this->assertEquals([new FileResource($resource)], $catalogue->getResources());
    }

    /**
     * @expectedException \Symfony\Component\Translation\Exception\NotFoundResourceException
     */
    public function test_load_non_existing_resource()
    {
        $loader = new JsonFileLoader;
        $resource = __DIR__.'/../fixtures/non-existing.json';
        $loader->load($resource, 'en', 'domain1');
    }

    /**
     * @expectedException           \Symfony\Component\Translation\Exception\InvalidResourceException
     *
     * @expectedExceptionMessage    Error parsing JSON - Syntax error, malformed JSON
     */
    public function test_parse_exception()
    {
        $loader = new JsonFileLoader;
        $resource = __DIR__.'/../fixtures/malformed.json';
        $loader->load($resource, 'en', 'domain1');
    }
}
