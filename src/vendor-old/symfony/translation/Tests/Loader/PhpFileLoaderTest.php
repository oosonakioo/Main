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
use Symfony\Component\Translation\Loader\PhpFileLoader;

class PhpFileLoaderTest extends \PHPUnit_Framework_TestCase
{
    public function test_load()
    {
        $loader = new PhpFileLoader;
        $resource = __DIR__.'/../fixtures/resources.php';
        $catalogue = $loader->load($resource, 'en', 'domain1');

        $this->assertEquals(['foo' => 'bar'], $catalogue->all('domain1'));
        $this->assertEquals('en', $catalogue->getLocale());
        $this->assertEquals([new FileResource($resource)], $catalogue->getResources());
    }

    /**
     * @expectedException \Symfony\Component\Translation\Exception\NotFoundResourceException
     */
    public function test_load_non_existing_resource()
    {
        $loader = new PhpFileLoader;
        $resource = __DIR__.'/../fixtures/non-existing.php';
        $loader->load($resource, 'en', 'domain1');
    }

    /**
     * @expectedException \Symfony\Component\Translation\Exception\InvalidResourceException
     */
    public function test_load_throws_an_exception_if_file_not_local()
    {
        $loader = new PhpFileLoader;
        $resource = 'http://example.com/resources.php';
        $loader->load($resource, 'en', 'domain1');
    }
}
