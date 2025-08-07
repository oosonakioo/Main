<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\Translation\Tests\Catalogue;

use Symfony\Component\Translation\MessageCatalogue;
use Symfony\Component\Translation\MessageCatalogueInterface;

abstract class AbstractOperationTest extends \PHPUnit_Framework_TestCase
{
    public function test_get_empty_domains()
    {
        $this->assertEquals(
            [],
            $this->createOperation(
                new MessageCatalogue('en'),
                new MessageCatalogue('en')
            )->getDomains()
        );
    }

    public function test_get_merged_domains()
    {
        $this->assertEquals(
            ['a', 'b', 'c'],
            $this->createOperation(
                new MessageCatalogue('en', ['a' => [], 'b' => []]),
                new MessageCatalogue('en', ['b' => [], 'c' => []])
            )->getDomains()
        );
    }

    public function test_get_messages_from_unknown_domain()
    {
        $this->setExpectedException('InvalidArgumentException');
        $this->createOperation(
            new MessageCatalogue('en'),
            new MessageCatalogue('en')
        )->getMessages('domain');
    }

    public function test_get_empty_messages()
    {
        $this->assertEquals(
            [],
            $this->createOperation(
                new MessageCatalogue('en', ['a' => []]),
                new MessageCatalogue('en')
            )->getMessages('a')
        );
    }

    public function test_get_empty_result()
    {
        $this->assertEquals(
            new MessageCatalogue('en'),
            $this->createOperation(
                new MessageCatalogue('en'),
                new MessageCatalogue('en')
            )->getResult()
        );
    }

    abstract protected function createOperation(MessageCatalogueInterface $source, MessageCatalogueInterface $target);
}
