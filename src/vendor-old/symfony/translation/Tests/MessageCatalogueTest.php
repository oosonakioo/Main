<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\Translation\Tests;

use Symfony\Component\Translation\MessageCatalogue;

class MessageCatalogueTest extends \PHPUnit_Framework_TestCase
{
    public function test_get_locale()
    {
        $catalogue = new MessageCatalogue('en');

        $this->assertEquals('en', $catalogue->getLocale());
    }

    public function test_get_domains()
    {
        $catalogue = new MessageCatalogue('en', ['domain1' => [], 'domain2' => []]);

        $this->assertEquals(['domain1', 'domain2'], $catalogue->getDomains());
    }

    public function test_all()
    {
        $catalogue = new MessageCatalogue('en', $messages = ['domain1' => ['foo' => 'foo'], 'domain2' => ['bar' => 'bar']]);

        $this->assertEquals(['foo' => 'foo'], $catalogue->all('domain1'));
        $this->assertEquals([], $catalogue->all('domain88'));
        $this->assertEquals($messages, $catalogue->all());
    }

    public function test_has()
    {
        $catalogue = new MessageCatalogue('en', ['domain1' => ['foo' => 'foo'], 'domain2' => ['bar' => 'bar']]);

        $this->assertTrue($catalogue->has('foo', 'domain1'));
        $this->assertFalse($catalogue->has('bar', 'domain1'));
        $this->assertFalse($catalogue->has('foo', 'domain88'));
    }

    public function test_get_set()
    {
        $catalogue = new MessageCatalogue('en', ['domain1' => ['foo' => 'foo'], 'domain2' => ['bar' => 'bar']]);
        $catalogue->set('foo1', 'foo1', 'domain1');

        $this->assertEquals('foo', $catalogue->get('foo', 'domain1'));
        $this->assertEquals('foo1', $catalogue->get('foo1', 'domain1'));
    }

    public function test_add()
    {
        $catalogue = new MessageCatalogue('en', ['domain1' => ['foo' => 'foo'], 'domain2' => ['bar' => 'bar']]);
        $catalogue->add(['foo1' => 'foo1'], 'domain1');

        $this->assertEquals('foo', $catalogue->get('foo', 'domain1'));
        $this->assertEquals('foo1', $catalogue->get('foo1', 'domain1'));

        $catalogue->add(['foo' => 'bar'], 'domain1');
        $this->assertEquals('bar', $catalogue->get('foo', 'domain1'));
        $this->assertEquals('foo1', $catalogue->get('foo1', 'domain1'));

        $catalogue->add(['foo' => 'bar'], 'domain88');
        $this->assertEquals('bar', $catalogue->get('foo', 'domain88'));
    }

    public function test_replace()
    {
        $catalogue = new MessageCatalogue('en', ['domain1' => ['foo' => 'foo'], 'domain2' => ['bar' => 'bar']]);
        $catalogue->replace($messages = ['foo1' => 'foo1'], 'domain1');

        $this->assertEquals($messages, $catalogue->all('domain1'));
    }

    public function test_add_catalogue()
    {
        $r = $this->getMock('Symfony\Component\Config\Resource\ResourceInterface');
        $r->expects($this->any())->method('__toString')->will($this->returnValue('r'));

        $r1 = $this->getMock('Symfony\Component\Config\Resource\ResourceInterface');
        $r1->expects($this->any())->method('__toString')->will($this->returnValue('r1'));

        $catalogue = new MessageCatalogue('en', ['domain1' => ['foo' => 'foo'], 'domain2' => ['bar' => 'bar']]);
        $catalogue->addResource($r);

        $catalogue1 = new MessageCatalogue('en', ['domain1' => ['foo1' => 'foo1']]);
        $catalogue1->addResource($r1);

        $catalogue->addCatalogue($catalogue1);

        $this->assertEquals('foo', $catalogue->get('foo', 'domain1'));
        $this->assertEquals('foo1', $catalogue->get('foo1', 'domain1'));

        $this->assertEquals([$r, $r1], $catalogue->getResources());
    }

    public function test_add_fallback_catalogue()
    {
        $r = $this->getMock('Symfony\Component\Config\Resource\ResourceInterface');
        $r->expects($this->any())->method('__toString')->will($this->returnValue('r'));

        $r1 = $this->getMock('Symfony\Component\Config\Resource\ResourceInterface');
        $r1->expects($this->any())->method('__toString')->will($this->returnValue('r1'));

        $catalogue = new MessageCatalogue('en_US', ['domain1' => ['foo' => 'foo'], 'domain2' => ['bar' => 'bar']]);
        $catalogue->addResource($r);

        $catalogue1 = new MessageCatalogue('en', ['domain1' => ['foo' => 'bar', 'foo1' => 'foo1']]);
        $catalogue1->addResource($r1);

        $catalogue->addFallbackCatalogue($catalogue1);

        $this->assertEquals('foo', $catalogue->get('foo', 'domain1'));
        $this->assertEquals('foo1', $catalogue->get('foo1', 'domain1'));

        $this->assertEquals([$r, $r1], $catalogue->getResources());
    }

    /**
     * @expectedException \LogicException
     */
    public function test_add_fallback_catalogue_with_parent_circular_reference()
    {
        $main = new MessageCatalogue('en_US');
        $fallback = new MessageCatalogue('fr_FR');

        $fallback->addFallbackCatalogue($main);
        $main->addFallbackCatalogue($fallback);
    }

    /**
     * @expectedException \LogicException
     */
    public function test_add_fallback_catalogue_with_fallback_circular_reference()
    {
        $fr = new MessageCatalogue('fr');
        $en = new MessageCatalogue('en');
        $es = new MessageCatalogue('es');

        $fr->addFallbackCatalogue($en);
        $es->addFallbackCatalogue($en);
        $en->addFallbackCatalogue($fr);
    }

    /**
     * @expectedException \LogicException
     */
    public function test_add_catalogue_when_locale_is_not_the_same_as_the_current_one()
    {
        $catalogue = new MessageCatalogue('en');
        $catalogue->addCatalogue(new MessageCatalogue('fr', []));
    }

    public function test_get_add_resource()
    {
        $catalogue = new MessageCatalogue('en');
        $r = $this->getMock('Symfony\Component\Config\Resource\ResourceInterface');
        $r->expects($this->any())->method('__toString')->will($this->returnValue('r'));
        $catalogue->addResource($r);
        $catalogue->addResource($r);
        $r1 = $this->getMock('Symfony\Component\Config\Resource\ResourceInterface');
        $r1->expects($this->any())->method('__toString')->will($this->returnValue('r1'));
        $catalogue->addResource($r1);

        $this->assertEquals([$r, $r1], $catalogue->getResources());
    }

    public function test_metadata_delete()
    {
        $catalogue = new MessageCatalogue('en');
        $this->assertEquals([], $catalogue->getMetadata('', ''), 'Metadata is empty');
        $catalogue->deleteMetadata('key', 'messages');
        $catalogue->deleteMetadata('', 'messages');
        $catalogue->deleteMetadata();
    }

    public function test_metadata_set_get_delete()
    {
        $catalogue = new MessageCatalogue('en');
        $catalogue->setMetadata('key', 'value');
        $this->assertEquals('value', $catalogue->getMetadata('key', 'messages'), "Metadata 'key' = 'value'");

        $catalogue->setMetadata('key2', []);
        $this->assertEquals([], $catalogue->getMetadata('key2', 'messages'), 'Metadata key2 is array');

        $catalogue->deleteMetadata('key2', 'messages');
        $this->assertNull($catalogue->getMetadata('key2', 'messages'), 'Metadata key2 should is deleted.');

        $catalogue->deleteMetadata('key2', 'domain');
        $this->assertNull($catalogue->getMetadata('key2', 'domain'), 'Metadata key2 should is deleted.');
    }

    public function test_metadata_merge()
    {
        $cat1 = new MessageCatalogue('en');
        $cat1->setMetadata('a', 'b');
        $this->assertEquals(['messages' => ['a' => 'b']], $cat1->getMetadata('', ''), 'Cat1 contains messages metadata.');

        $cat2 = new MessageCatalogue('en');
        $cat2->setMetadata('b', 'c', 'domain');
        $this->assertEquals(['domain' => ['b' => 'c']], $cat2->getMetadata('', ''), 'Cat2 contains domain metadata.');

        $cat1->addCatalogue($cat2);
        $this->assertEquals(['messages' => ['a' => 'b'], 'domain' => ['b' => 'c']], $cat1->getMetadata('', ''), 'Cat1 contains merged metadata.');
    }
}
