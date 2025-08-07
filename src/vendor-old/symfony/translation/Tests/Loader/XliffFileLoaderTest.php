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
use Symfony\Component\Translation\Loader\XliffFileLoader;

class XliffFileLoaderTest extends \PHPUnit_Framework_TestCase
{
    public function test_load()
    {
        $loader = new XliffFileLoader;
        $resource = __DIR__.'/../fixtures/resources.xlf';
        $catalogue = $loader->load($resource, 'en', 'domain1');

        $this->assertEquals('en', $catalogue->getLocale());
        $this->assertEquals([new FileResource($resource)], $catalogue->getResources());
        $this->assertSame([], libxml_get_errors());
        $this->assertContainsOnly('string', $catalogue->all('domain1'));
    }

    public function test_load_with_internal_errors_enabled()
    {
        $internalErrors = libxml_use_internal_errors(true);

        $this->assertSame([], libxml_get_errors());

        $loader = new XliffFileLoader;
        $resource = __DIR__.'/../fixtures/resources.xlf';
        $catalogue = $loader->load($resource, 'en', 'domain1');

        $this->assertEquals('en', $catalogue->getLocale());
        $this->assertEquals([new FileResource($resource)], $catalogue->getResources());
        $this->assertSame([], libxml_get_errors());

        libxml_clear_errors();
        libxml_use_internal_errors($internalErrors);
    }

    public function test_load_with_external_entities_disabled()
    {
        $disableEntities = libxml_disable_entity_loader(true);

        $loader = new XliffFileLoader;
        $resource = __DIR__.'/../fixtures/resources.xlf';
        $catalogue = $loader->load($resource, 'en', 'domain1');

        libxml_disable_entity_loader($disableEntities);

        $this->assertEquals('en', $catalogue->getLocale());
        $this->assertEquals([new FileResource($resource)], $catalogue->getResources());
    }

    public function test_load_with_resname()
    {
        $loader = new XliffFileLoader;
        $catalogue = $loader->load(__DIR__.'/../fixtures/resname.xlf', 'en', 'domain1');

        $this->assertEquals(['foo' => 'bar', 'bar' => 'baz', 'baz' => 'foo'], $catalogue->all('domain1'));
    }

    public function test_incomplete_resource()
    {
        $loader = new XliffFileLoader;
        $catalogue = $loader->load(__DIR__.'/../fixtures/resources.xlf', 'en', 'domain1');

        $this->assertEquals(['foo' => 'bar', 'extra' => 'extra', 'key' => '', 'test' => 'with'], $catalogue->all('domain1'));
    }

    public function test_encoding()
    {
        $loader = new XliffFileLoader;
        $catalogue = $loader->load(__DIR__.'/../fixtures/encoding.xlf', 'en', 'domain1');

        $this->assertEquals(utf8_decode('föö'), $catalogue->get('bar', 'domain1'));
        $this->assertEquals(utf8_decode('bär'), $catalogue->get('foo', 'domain1'));
        $this->assertEquals(['notes' => [['content' => utf8_decode('bäz')]]], $catalogue->getMetadata('foo', 'domain1'));
    }

    public function test_target_attributes_are_stored_correctly()
    {
        $loader = new XliffFileLoader;
        $catalogue = $loader->load(__DIR__.'/../fixtures/with-attributes.xlf', 'en', 'domain1');

        $metadata = $catalogue->getMetadata('foo', 'domain1');
        $this->assertEquals('translated', $metadata['target-attributes']['state']);
    }

    /**
     * @expectedException \Symfony\Component\Translation\Exception\InvalidResourceException
     */
    public function test_load_invalid_resource()
    {
        $loader = new XliffFileLoader;
        $loader->load(__DIR__.'/../fixtures/resources.php', 'en', 'domain1');
    }

    /**
     * @expectedException \Symfony\Component\Translation\Exception\InvalidResourceException
     */
    public function test_load_resource_does_not_validate()
    {
        $loader = new XliffFileLoader;
        $loader->load(__DIR__.'/../fixtures/non-valid.xlf', 'en', 'domain1');
    }

    /**
     * @expectedException \Symfony\Component\Translation\Exception\NotFoundResourceException
     */
    public function test_load_non_existing_resource()
    {
        $loader = new XliffFileLoader;
        $resource = __DIR__.'/../fixtures/non-existing.xlf';
        $loader->load($resource, 'en', 'domain1');
    }

    /**
     * @expectedException \Symfony\Component\Translation\Exception\InvalidResourceException
     */
    public function test_load_throws_an_exception_if_file_not_local()
    {
        $loader = new XliffFileLoader;
        $resource = 'http://example.com/resources.xlf';
        $loader->load($resource, 'en', 'domain1');
    }

    /**
     * @expectedException        \Symfony\Component\Translation\Exception\InvalidResourceException
     *
     * @expectedExceptionMessage Document types are not allowed.
     */
    public function test_doc_type_is_not_allowed()
    {
        $loader = new XliffFileLoader;
        $loader->load(__DIR__.'/../fixtures/withdoctype.xlf', 'en', 'domain1');
    }

    public function test_parse_empty_file()
    {
        $loader = new XliffFileLoader;
        $resource = __DIR__.'/../fixtures/empty.xlf';
        $this->setExpectedException('Symfony\Component\Translation\Exception\InvalidResourceException', sprintf('Unable to load "%s":', $resource));
        $loader->load($resource, 'en', 'domain1');
    }

    public function test_load_notes()
    {
        $loader = new XliffFileLoader;
        $catalogue = $loader->load(__DIR__.'/../fixtures/withnote.xlf', 'en', 'domain1');

        $this->assertEquals(['notes' => [['priority' => 1, 'content' => 'foo']]], $catalogue->getMetadata('foo', 'domain1'));
        // message without target
        $this->assertEquals(['notes' => [['content' => 'bar', 'from' => 'foo']]], $catalogue->getMetadata('extra', 'domain1'));
        // message with empty target
        $this->assertEquals(['notes' => [['content' => 'baz'], ['priority' => 2, 'from' => 'bar', 'content' => 'qux']]], $catalogue->getMetadata('key', 'domain1'));
    }

    public function test_load_version2()
    {
        $loader = new XliffFileLoader;
        $resource = __DIR__.'/../fixtures/resources-2.0.xlf';
        $catalogue = $loader->load($resource, 'en', 'domain1');

        $this->assertEquals('en', $catalogue->getLocale());
        $this->assertEquals([new FileResource($resource)], $catalogue->getResources());
        $this->assertSame([], libxml_get_errors());

        $domains = $catalogue->all();
        $this->assertCount(3, $domains['domain1']);
        $this->assertContainsOnly('string', $catalogue->all('domain1'));

        // target attributes
        $this->assertEquals(['target-attributes' => ['order' => 1]], $catalogue->getMetadata('bar', 'domain1'));
    }
}
