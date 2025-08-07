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
use Symfony\Component\Translation\Loader\PoFileLoader;

class PoFileLoaderTest extends \PHPUnit_Framework_TestCase
{
    public function test_load()
    {
        $loader = new PoFileLoader;
        $resource = __DIR__.'/../fixtures/resources.po';
        $catalogue = $loader->load($resource, 'en', 'domain1');

        $this->assertEquals(['foo' => 'bar'], $catalogue->all('domain1'));
        $this->assertEquals('en', $catalogue->getLocale());
        $this->assertEquals([new FileResource($resource)], $catalogue->getResources());
    }

    public function test_load_plurals()
    {
        $loader = new PoFileLoader;
        $resource = __DIR__.'/../fixtures/plurals.po';
        $catalogue = $loader->load($resource, 'en', 'domain1');

        $this->assertEquals(['foo' => 'bar', 'foos' => 'bar|bars'], $catalogue->all('domain1'));
        $this->assertEquals('en', $catalogue->getLocale());
        $this->assertEquals([new FileResource($resource)], $catalogue->getResources());
    }

    public function test_load_does_nothing_if_empty()
    {
        $loader = new PoFileLoader;
        $resource = __DIR__.'/../fixtures/empty.po';
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
        $loader = new PoFileLoader;
        $resource = __DIR__.'/../fixtures/non-existing.po';
        $loader->load($resource, 'en', 'domain1');
    }

    public function test_load_empty_translation()
    {
        $loader = new PoFileLoader;
        $resource = __DIR__.'/../fixtures/empty-translation.po';
        $catalogue = $loader->load($resource, 'en', 'domain1');

        $this->assertEquals(['foo' => ''], $catalogue->all('domain1'));
        $this->assertEquals('en', $catalogue->getLocale());
        $this->assertEquals([new FileResource($resource)], $catalogue->getResources());
    }

    public function test_escaped_id()
    {
        $loader = new PoFileLoader;
        $resource = __DIR__.'/../fixtures/escaped-id.po';
        $catalogue = $loader->load($resource, 'en', 'domain1');

        $messages = $catalogue->all('domain1');
        $this->assertArrayHasKey('escaped "foo"', $messages);
        $this->assertEquals('escaped "bar"', $messages['escaped "foo"']);
    }

    public function test_escaped_id_plurals()
    {
        $loader = new PoFileLoader;
        $resource = __DIR__.'/../fixtures/escaped-id-plurals.po';
        $catalogue = $loader->load($resource, 'en', 'domain1');

        $messages = $catalogue->all('domain1');
        $this->assertArrayHasKey('escaped "foo"', $messages);
        $this->assertArrayHasKey('escaped "foos"', $messages);
        $this->assertEquals('escaped "bar"', $messages['escaped "foo"']);
        $this->assertEquals('escaped "bar"|escaped "bars"', $messages['escaped "foos"']);
    }

    public function test_skip_fuzzy_translations()
    {
        $loader = new PoFileLoader;
        $resource = __DIR__.'/../fixtures/fuzzy-translations.po';
        $catalogue = $loader->load($resource, 'en', 'domain1');

        $messages = $catalogue->all('domain1');
        $this->assertArrayHasKey('foo1', $messages);
        $this->assertArrayNotHasKey('foo2', $messages);
        $this->assertArrayHasKey('foo3', $messages);
    }
}
