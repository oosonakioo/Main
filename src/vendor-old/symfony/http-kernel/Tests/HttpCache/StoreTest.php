<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\HttpKernel\Tests\HttpCache;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\HttpCache\Store;

class StoreTest extends \PHPUnit_Framework_TestCase
{
    protected $request;

    protected $response;

    /**
     * @var Store
     */
    protected $store;

    protected function setUp()
    {
        $this->request = Request::create('/');
        $this->response = new Response('hello world', 200, []);

        HttpCacheTestCase::clearDirectory(sys_get_temp_dir().'/http_cache');

        $this->store = new Store(sys_get_temp_dir().'/http_cache');
    }

    protected function tearDown()
    {
        $this->store = null;
        $this->request = null;
        $this->response = null;

        HttpCacheTestCase::clearDirectory(sys_get_temp_dir().'/http_cache');
    }

    public function test_reads_an_empty_array_with_read_when_nothing_cached_at_key()
    {
        $this->assertEmpty($this->getStoreMetadata('/nothing'));
    }

    public function test_unlock_file_that_does_exist()
    {
        $cacheKey = $this->storeSimpleEntry();
        $this->store->lock($this->request);

        $this->assertTrue($this->store->unlock($this->request));
    }

    public function test_unlock_file_that_does_not_exist()
    {
        $this->assertFalse($this->store->unlock($this->request));
    }

    public function test_removes_entries_for_key_with_purge()
    {
        $request = Request::create('/foo');
        $this->store->write($request, new Response('foo'));

        $metadata = $this->getStoreMetadata($request);
        $this->assertNotEmpty($metadata);

        $this->assertTrue($this->store->purge('/foo'));
        $this->assertEmpty($this->getStoreMetadata($request));

        // cached content should be kept after purging
        $path = $this->store->getPath($metadata[0][1]['x-content-digest'][0]);
        $this->assertTrue(is_file($path));

        $this->assertFalse($this->store->purge('/bar'));
    }

    public function test_stores_a_cache_entry()
    {
        $cacheKey = $this->storeSimpleEntry();

        $this->assertNotEmpty($this->getStoreMetadata($cacheKey));
    }

    public function test_sets_the_x_content_digest_response_header_before_storing()
    {
        $cacheKey = $this->storeSimpleEntry();
        $entries = $this->getStoreMetadata($cacheKey);
        [$req, $res] = $entries[0];

        $this->assertEquals('en9f86d081884c7d659a2feaa0c55ad015a3bf4f1b2b0b822cd15d6c15b0f00a08', $res['x-content-digest'][0]);
    }

    public function test_finds_a_stored_entry_with_lookup()
    {
        $this->storeSimpleEntry();
        $response = $this->store->lookup($this->request);

        $this->assertNotNull($response);
        $this->assertInstanceOf('Symfony\Component\HttpFoundation\Response', $response);
    }

    public function test_does_not_find_an_entry_with_lookup_when_none_exists()
    {
        $request = Request::create('/test', 'get', [], [], [], ['HTTP_FOO' => 'Foo', 'HTTP_BAR' => 'Bar']);

        $this->assertNull($this->store->lookup($request));
    }

    public function test_canonizes_urls_for_cache_keys()
    {
        $this->storeSimpleEntry($path = '/test?x=y&p=q');
        $hitsReq = Request::create($path);
        $missReq = Request::create('/test?p=x');

        $this->assertNotNull($this->store->lookup($hitsReq));
        $this->assertNull($this->store->lookup($missReq));
    }

    public function test_does_not_find_an_entry_with_lookup_when_the_body_does_not_exist()
    {
        $this->storeSimpleEntry();
        $this->assertNotNull($this->response->headers->get('X-Content-Digest'));
        $path = $this->getStorePath($this->response->headers->get('X-Content-Digest'));
        @unlink($path);
        $this->assertNull($this->store->lookup($this->request));
    }

    public function test_restores_response_headers_properly_with_lookup()
    {
        $this->storeSimpleEntry();
        $response = $this->store->lookup($this->request);

        $this->assertEquals($response->headers->all(), array_merge(['content-length' => 4, 'x-body-file' => [$this->getStorePath($response->headers->get('X-Content-Digest'))]], $this->response->headers->all()));
    }

    public function test_restores_response_content_from_entity_store_with_lookup()
    {
        $this->storeSimpleEntry();
        $response = $this->store->lookup($this->request);
        $this->assertEquals($this->getStorePath('en'.hash('sha256', 'test')), $response->getContent());
    }

    public function test_invalidates_meta_and_entity_store_entries_with_invalidate()
    {
        $this->storeSimpleEntry();
        $this->store->invalidate($this->request);
        $response = $this->store->lookup($this->request);
        $this->assertInstanceOf('Symfony\Component\HttpFoundation\Response', $response);
        $this->assertFalse($response->isFresh());
    }

    public function test_succeeds_quietly_when_invalidate_called_with_no_matching_entries()
    {
        $req = Request::create('/test');
        $this->store->invalidate($req);
        $this->assertNull($this->store->lookup($this->request));
    }

    public function test_does_not_return_entries_that_vary_with_lookup()
    {
        $req1 = Request::create('/test', 'get', [], [], [], ['HTTP_FOO' => 'Foo', 'HTTP_BAR' => 'Bar']);
        $req2 = Request::create('/test', 'get', [], [], [], ['HTTP_FOO' => 'Bling', 'HTTP_BAR' => 'Bam']);
        $res = new Response('test', 200, ['Vary' => 'Foo Bar']);
        $this->store->write($req1, $res);

        $this->assertNull($this->store->lookup($req2));
    }

    public function test_does_not_return_entries_that_slightly_vary_with_lookup()
    {
        $req1 = Request::create('/test', 'get', [], [], [], ['HTTP_FOO' => 'Foo', 'HTTP_BAR' => 'Bar']);
        $req2 = Request::create('/test', 'get', [], [], [], ['HTTP_FOO' => 'Foo', 'HTTP_BAR' => 'Bam']);
        $res = new Response('test', 200, ['Vary' => ['Foo', 'Bar']]);
        $this->store->write($req1, $res);

        $this->assertNull($this->store->lookup($req2));
    }

    public function test_stores_multiple_responses_for_each_vary_combination()
    {
        $req1 = Request::create('/test', 'get', [], [], [], ['HTTP_FOO' => 'Foo', 'HTTP_BAR' => 'Bar']);
        $res1 = new Response('test 1', 200, ['Vary' => 'Foo Bar']);
        $key = $this->store->write($req1, $res1);

        $req2 = Request::create('/test', 'get', [], [], [], ['HTTP_FOO' => 'Bling', 'HTTP_BAR' => 'Bam']);
        $res2 = new Response('test 2', 200, ['Vary' => 'Foo Bar']);
        $this->store->write($req2, $res2);

        $req3 = Request::create('/test', 'get', [], [], [], ['HTTP_FOO' => 'Baz', 'HTTP_BAR' => 'Boom']);
        $res3 = new Response('test 3', 200, ['Vary' => 'Foo Bar']);
        $this->store->write($req3, $res3);

        $this->assertEquals($this->getStorePath('en'.hash('sha256', 'test 3')), $this->store->lookup($req3)->getContent());
        $this->assertEquals($this->getStorePath('en'.hash('sha256', 'test 2')), $this->store->lookup($req2)->getContent());
        $this->assertEquals($this->getStorePath('en'.hash('sha256', 'test 1')), $this->store->lookup($req1)->getContent());

        $this->assertCount(3, $this->getStoreMetadata($key));
    }

    public function test_overwrites_non_varying_response_with_store()
    {
        $req1 = Request::create('/test', 'get', [], [], [], ['HTTP_FOO' => 'Foo', 'HTTP_BAR' => 'Bar']);
        $res1 = new Response('test 1', 200, ['Vary' => 'Foo Bar']);
        $key = $this->store->write($req1, $res1);
        $this->assertEquals($this->getStorePath('en'.hash('sha256', 'test 1')), $this->store->lookup($req1)->getContent());

        $req2 = Request::create('/test', 'get', [], [], [], ['HTTP_FOO' => 'Bling', 'HTTP_BAR' => 'Bam']);
        $res2 = new Response('test 2', 200, ['Vary' => 'Foo Bar']);
        $this->store->write($req2, $res2);
        $this->assertEquals($this->getStorePath('en'.hash('sha256', 'test 2')), $this->store->lookup($req2)->getContent());

        $req3 = Request::create('/test', 'get', [], [], [], ['HTTP_FOO' => 'Foo', 'HTTP_BAR' => 'Bar']);
        $res3 = new Response('test 3', 200, ['Vary' => 'Foo Bar']);
        $key = $this->store->write($req3, $res3);
        $this->assertEquals($this->getStorePath('en'.hash('sha256', 'test 3')), $this->store->lookup($req3)->getContent());

        $this->assertCount(2, $this->getStoreMetadata($key));
    }

    public function test_locking()
    {
        $req = Request::create('/test', 'get', [], [], [], ['HTTP_FOO' => 'Foo', 'HTTP_BAR' => 'Bar']);
        $this->assertTrue($this->store->lock($req));

        $path = $this->store->lock($req);
        $this->assertTrue($this->store->isLocked($req));

        $this->store->unlock($req);
        $this->assertFalse($this->store->isLocked($req));
    }

    protected function storeSimpleEntry($path = null, $headers = [])
    {
        if ($path === null) {
            $path = '/test';
        }

        $this->request = Request::create($path, 'get', [], [], [], $headers);
        $this->response = new Response('test', 200, ['Cache-Control' => 'max-age=420']);

        return $this->store->write($this->request, $this->response);
    }

    protected function getStoreMetadata($key)
    {
        $r = new \ReflectionObject($this->store);
        $m = $r->getMethod('getMetadata');
        $m->setAccessible(true);

        if ($key instanceof Request) {
            $m1 = $r->getMethod('getCacheKey');
            $m1->setAccessible(true);
            $key = $m1->invoke($this->store, $key);
        }

        return $m->invoke($this->store, $key);
    }

    protected function getStorePath($key)
    {
        $r = new \ReflectionObject($this->store);
        $m = $r->getMethod('getPath');
        $m->setAccessible(true);

        return $m->invoke($this->store, $key);
    }
}
