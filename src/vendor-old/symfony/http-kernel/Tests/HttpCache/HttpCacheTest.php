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
use Symfony\Component\HttpKernel\HttpCache\HttpCache;
use Symfony\Component\HttpKernel\HttpKernelInterface;

/**
 * @group time-sensitive
 */
class HttpCacheTest extends HttpCacheTestCase
{
    public function test_terminate_delegates_termination_only_for_terminable_interface()
    {
        $storeMock = $this->getMockBuilder('Symfony\\Component\\HttpKernel\\HttpCache\\StoreInterface')
            ->disableOriginalConstructor()
            ->getMock();

        // does not implement TerminableInterface
        $kernel = new TestKernel;
        $httpCache = new HttpCache($kernel, $storeMock);
        $httpCache->terminate(Request::create('/'), new Response);

        $this->assertFalse($kernel->terminateCalled, 'terminate() is never called if the kernel class does not implement TerminableInterface');

        // implements TerminableInterface
        $kernelMock = $this->getMockBuilder('Symfony\\Component\\HttpKernel\\Kernel')
            ->disableOriginalConstructor()
            ->setMethods(['terminate', 'registerBundles', 'registerContainerConfiguration'])
            ->getMock();

        $kernelMock->expects($this->once())
            ->method('terminate');

        $kernel = new HttpCache($kernelMock, $storeMock);
        $kernel->terminate(Request::create('/'), new Response);
    }

    public function test_passes_on_non_get_head_requests()
    {
        $this->setNextResponse(200);
        $this->request('POST', '/');
        $this->assertHttpKernelIsCalled();
        $this->assertResponseOk();
        $this->assertTraceContains('pass');
        $this->assertFalse($this->response->headers->has('Age'));
    }

    public function test_invalidates_on_post_put_delete_requests()
    {
        foreach (['post', 'put', 'delete'] as $method) {
            $this->setNextResponse(200);
            $this->request($method, '/');

            $this->assertHttpKernelIsCalled();
            $this->assertResponseOk();
            $this->assertTraceContains('invalidate');
            $this->assertTraceContains('pass');
        }
    }

    public function test_does_not_cache_with_authorization_request_header_and_non_public_response()
    {
        $this->setNextResponse(200, ['ETag' => '"Foo"']);
        $this->request('GET', '/', ['HTTP_AUTHORIZATION' => 'basic foobarbaz']);

        $this->assertHttpKernelIsCalled();
        $this->assertResponseOk();
        $this->assertEquals('private', $this->response->headers->get('Cache-Control'));

        $this->assertTraceContains('miss');
        $this->assertTraceNotContains('store');
        $this->assertFalse($this->response->headers->has('Age'));
    }

    public function test_does_cache_with_authorization_request_header_and_public_response()
    {
        $this->setNextResponse(200, ['Cache-Control' => 'public', 'ETag' => '"Foo"']);
        $this->request('GET', '/', ['HTTP_AUTHORIZATION' => 'basic foobarbaz']);

        $this->assertHttpKernelIsCalled();
        $this->assertResponseOk();
        $this->assertTraceContains('miss');
        $this->assertTraceContains('store');
        $this->assertTrue($this->response->headers->has('Age'));
        $this->assertEquals('public', $this->response->headers->get('Cache-Control'));
    }

    public function test_does_not_cache_with_cookie_header_and_non_public_response()
    {
        $this->setNextResponse(200, ['ETag' => '"Foo"']);
        $this->request('GET', '/', [], ['foo' => 'bar']);

        $this->assertHttpKernelIsCalled();
        $this->assertResponseOk();
        $this->assertEquals('private', $this->response->headers->get('Cache-Control'));
        $this->assertTraceContains('miss');
        $this->assertTraceNotContains('store');
        $this->assertFalse($this->response->headers->has('Age'));
    }

    public function test_does_not_cache_requests_with_a_cookie_header()
    {
        $this->setNextResponse(200);
        $this->request('GET', '/', [], ['foo' => 'bar']);

        $this->assertHttpKernelIsCalled();
        $this->assertResponseOk();
        $this->assertEquals('private', $this->response->headers->get('Cache-Control'));
        $this->assertTraceContains('miss');
        $this->assertTraceNotContains('store');
        $this->assertFalse($this->response->headers->has('Age'));
    }

    public function test_responds_with304_when_if_modified_since_matches_last_modified()
    {
        $time = \DateTime::createFromFormat('U', time());

        $this->setNextResponse(200, ['Cache-Control' => 'public', 'Last-Modified' => $time->format(DATE_RFC2822), 'Content-Type' => 'text/plain'], 'Hello World');
        $this->request('GET', '/', ['HTTP_IF_MODIFIED_SINCE' => $time->format(DATE_RFC2822)]);

        $this->assertHttpKernelIsCalled();
        $this->assertEquals(304, $this->response->getStatusCode());
        $this->assertEquals('', $this->response->headers->get('Content-Type'));
        $this->assertEmpty($this->response->getContent());
        $this->assertTraceContains('miss');
        $this->assertTraceContains('store');
    }

    public function test_responds_with304_when_if_none_match_matches_e_tag()
    {
        $this->setNextResponse(200, ['Cache-Control' => 'public', 'ETag' => '12345', 'Content-Type' => 'text/plain'], 'Hello World');
        $this->request('GET', '/', ['HTTP_IF_NONE_MATCH' => '12345']);

        $this->assertHttpKernelIsCalled();
        $this->assertEquals(304, $this->response->getStatusCode());
        $this->assertEquals('', $this->response->headers->get('Content-Type'));
        $this->assertTrue($this->response->headers->has('ETag'));
        $this->assertEmpty($this->response->getContent());
        $this->assertTraceContains('miss');
        $this->assertTraceContains('store');
    }

    public function test_responds_with304_only_if_if_none_match_and_if_modified_since_both_match()
    {
        $time = \DateTime::createFromFormat('U', time());

        $this->setNextResponse(200, [], '', function ($request, $response) use ($time) {
            $response->setStatusCode(200);
            $response->headers->set('ETag', '12345');
            $response->headers->set('Last-Modified', $time->format(DATE_RFC2822));
            $response->headers->set('Content-Type', 'text/plain');
            $response->setContent('Hello World');
        });

        // only ETag matches
        $t = \DateTime::createFromFormat('U', time() - 3600);
        $this->request('GET', '/', ['HTTP_IF_NONE_MATCH' => '12345', 'HTTP_IF_MODIFIED_SINCE' => $t->format(DATE_RFC2822)]);
        $this->assertHttpKernelIsCalled();
        $this->assertEquals(200, $this->response->getStatusCode());

        // only Last-Modified matches
        $this->request('GET', '/', ['HTTP_IF_NONE_MATCH' => '1234', 'HTTP_IF_MODIFIED_SINCE' => $time->format(DATE_RFC2822)]);
        $this->assertHttpKernelIsCalled();
        $this->assertEquals(200, $this->response->getStatusCode());

        // Both matches
        $this->request('GET', '/', ['HTTP_IF_NONE_MATCH' => '12345', 'HTTP_IF_MODIFIED_SINCE' => $time->format(DATE_RFC2822)]);
        $this->assertHttpKernelIsCalled();
        $this->assertEquals(304, $this->response->getStatusCode());
    }

    public function test_increments_max_age_when_no_date_is_specified_event_when_using_e_tag()
    {
        $this->setNextResponse(
            200,
            [
                'ETag' => '1234',
                'Cache-Control' => 'public, s-maxage=60',
            ]
        );

        $this->request('GET', '/');
        $this->assertHttpKernelIsCalled();
        $this->assertEquals(200, $this->response->getStatusCode());
        $this->assertTraceContains('miss');
        $this->assertTraceContains('store');

        sleep(2);

        $this->request('GET', '/');
        $this->assertHttpKernelIsNotCalled();
        $this->assertEquals(200, $this->response->getStatusCode());
        $this->assertTraceContains('fresh');
        $this->assertEquals(2, $this->response->headers->get('Age'));
    }

    public function test_validates_private_responses_cached_on_the_client()
    {
        $this->setNextResponse(200, [], '', function ($request, $response) {
            $etags = preg_split('/\s*,\s*/', $request->headers->get('IF_NONE_MATCH'));
            if ($request->cookies->has('authenticated')) {
                $response->headers->set('Cache-Control', 'private, no-store');
                $response->setETag('"private tag"');
                if (in_array('"private tag"', $etags)) {
                    $response->setStatusCode(304);
                } else {
                    $response->setStatusCode(200);
                    $response->headers->set('Content-Type', 'text/plain');
                    $response->setContent('private data');
                }
            } else {
                $response->headers->set('Cache-Control', 'public');
                $response->setETag('"public tag"');
                if (in_array('"public tag"', $etags)) {
                    $response->setStatusCode(304);
                } else {
                    $response->setStatusCode(200);
                    $response->headers->set('Content-Type', 'text/plain');
                    $response->setContent('public data');
                }
            }
        });

        $this->request('GET', '/');
        $this->assertHttpKernelIsCalled();
        $this->assertEquals(200, $this->response->getStatusCode());
        $this->assertEquals('"public tag"', $this->response->headers->get('ETag'));
        $this->assertEquals('public data', $this->response->getContent());
        $this->assertTraceContains('miss');
        $this->assertTraceContains('store');

        $this->request('GET', '/', [], ['authenticated' => '']);
        $this->assertHttpKernelIsCalled();
        $this->assertEquals(200, $this->response->getStatusCode());
        $this->assertEquals('"private tag"', $this->response->headers->get('ETag'));
        $this->assertEquals('private data', $this->response->getContent());
        $this->assertTraceContains('stale');
        $this->assertTraceContains('invalid');
        $this->assertTraceNotContains('store');
    }

    public function test_stores_responses_when_no_cache_request_directive_present()
    {
        $time = \DateTime::createFromFormat('U', time() + 5);

        $this->setNextResponse(200, ['Cache-Control' => 'public', 'Expires' => $time->format(DATE_RFC2822)]);
        $this->request('GET', '/', ['HTTP_CACHE_CONTROL' => 'no-cache']);

        $this->assertHttpKernelIsCalled();
        $this->assertTraceContains('store');
        $this->assertTrue($this->response->headers->has('Age'));
    }

    public function test_reloads_responses_when_cache_hits_but_no_cache_request_directive_present_when_allow_reload_is_set_true()
    {
        $count = 0;

        $this->setNextResponse(200, ['Cache-Control' => 'public, max-age=10000'], '', function ($request, $response) use (&$count) {
            $count++;
            $response->setContent($count == 1 ? 'Hello World' : 'Goodbye World');
        });

        $this->request('GET', '/');
        $this->assertEquals(200, $this->response->getStatusCode());
        $this->assertEquals('Hello World', $this->response->getContent());
        $this->assertTraceContains('store');

        $this->request('GET', '/');
        $this->assertEquals(200, $this->response->getStatusCode());
        $this->assertEquals('Hello World', $this->response->getContent());
        $this->assertTraceContains('fresh');

        $this->cacheConfig['allow_reload'] = true;
        $this->request('GET', '/', ['HTTP_CACHE_CONTROL' => 'no-cache']);
        $this->assertEquals(200, $this->response->getStatusCode());
        $this->assertEquals('Goodbye World', $this->response->getContent());
        $this->assertTraceContains('reload');
        $this->assertTraceContains('store');
    }

    public function test_does_not_reload_responses_when_allow_reload_is_set_false_default()
    {
        $count = 0;

        $this->setNextResponse(200, ['Cache-Control' => 'public, max-age=10000'], '', function ($request, $response) use (&$count) {
            $count++;
            $response->setContent($count == 1 ? 'Hello World' : 'Goodbye World');
        });

        $this->request('GET', '/');
        $this->assertEquals(200, $this->response->getStatusCode());
        $this->assertEquals('Hello World', $this->response->getContent());
        $this->assertTraceContains('store');

        $this->request('GET', '/');
        $this->assertEquals(200, $this->response->getStatusCode());
        $this->assertEquals('Hello World', $this->response->getContent());
        $this->assertTraceContains('fresh');

        $this->cacheConfig['allow_reload'] = false;
        $this->request('GET', '/', ['HTTP_CACHE_CONTROL' => 'no-cache']);
        $this->assertEquals(200, $this->response->getStatusCode());
        $this->assertEquals('Hello World', $this->response->getContent());
        $this->assertTraceNotContains('reload');

        $this->request('GET', '/', ['HTTP_CACHE_CONTROL' => 'no-cache']);
        $this->assertEquals(200, $this->response->getStatusCode());
        $this->assertEquals('Hello World', $this->response->getContent());
        $this->assertTraceNotContains('reload');
    }

    public function test_revalidates_fresh_cache_entry_when_max_age_request_directive_is_exceeded_when_allow_revalidate_option_is_set_true()
    {
        $count = 0;

        $this->setNextResponse(200, [], '', function ($request, $response) use (&$count) {
            $count++;
            $response->headers->set('Cache-Control', 'public, max-age=10000');
            $response->setETag($count);
            $response->setContent($count == 1 ? 'Hello World' : 'Goodbye World');
        });

        $this->request('GET', '/');
        $this->assertEquals(200, $this->response->getStatusCode());
        $this->assertEquals('Hello World', $this->response->getContent());
        $this->assertTraceContains('store');

        $this->request('GET', '/');
        $this->assertEquals(200, $this->response->getStatusCode());
        $this->assertEquals('Hello World', $this->response->getContent());
        $this->assertTraceContains('fresh');

        $this->cacheConfig['allow_revalidate'] = true;
        $this->request('GET', '/', ['HTTP_CACHE_CONTROL' => 'max-age=0']);
        $this->assertEquals(200, $this->response->getStatusCode());
        $this->assertEquals('Goodbye World', $this->response->getContent());
        $this->assertTraceContains('stale');
        $this->assertTraceContains('invalid');
        $this->assertTraceContains('store');
    }

    public function test_does_not_revalidate_fresh_cache_entry_when_enable_revalidate_option_is_set_false_default()
    {
        $count = 0;

        $this->setNextResponse(200, [], '', function ($request, $response) use (&$count) {
            $count++;
            $response->headers->set('Cache-Control', 'public, max-age=10000');
            $response->setETag($count);
            $response->setContent($count == 1 ? 'Hello World' : 'Goodbye World');
        });

        $this->request('GET', '/');
        $this->assertEquals(200, $this->response->getStatusCode());
        $this->assertEquals('Hello World', $this->response->getContent());
        $this->assertTraceContains('store');

        $this->request('GET', '/');
        $this->assertEquals(200, $this->response->getStatusCode());
        $this->assertEquals('Hello World', $this->response->getContent());
        $this->assertTraceContains('fresh');

        $this->cacheConfig['allow_revalidate'] = false;
        $this->request('GET', '/', ['HTTP_CACHE_CONTROL' => 'max-age=0']);
        $this->assertEquals(200, $this->response->getStatusCode());
        $this->assertEquals('Hello World', $this->response->getContent());
        $this->assertTraceNotContains('stale');
        $this->assertTraceNotContains('invalid');
        $this->assertTraceContains('fresh');

        $this->request('GET', '/', ['HTTP_CACHE_CONTROL' => 'max-age=0']);
        $this->assertEquals(200, $this->response->getStatusCode());
        $this->assertEquals('Hello World', $this->response->getContent());
        $this->assertTraceNotContains('stale');
        $this->assertTraceNotContains('invalid');
        $this->assertTraceContains('fresh');
    }

    public function test_fetches_response_from_backend_when_cache_misses()
    {
        $time = \DateTime::createFromFormat('U', time() + 5);
        $this->setNextResponse(200, ['Cache-Control' => 'public', 'Expires' => $time->format(DATE_RFC2822)]);

        $this->request('GET', '/');
        $this->assertEquals(200, $this->response->getStatusCode());
        $this->assertTraceContains('miss');
        $this->assertTrue($this->response->headers->has('Age'));
    }

    public function test_does_not_cache_some_status_code_responses()
    {
        foreach (array_merge(range(201, 202), range(204, 206), range(303, 305), range(400, 403), range(405, 409), range(411, 417), range(500, 505)) as $code) {
            $time = \DateTime::createFromFormat('U', time() + 5);
            $this->setNextResponse($code, ['Expires' => $time->format(DATE_RFC2822)]);

            $this->request('GET', '/');
            $this->assertEquals($code, $this->response->getStatusCode());
            $this->assertTraceNotContains('store');
            $this->assertFalse($this->response->headers->has('Age'));
        }
    }

    public function test_does_not_cache_responses_with_explicit_no_store_directive()
    {
        $time = \DateTime::createFromFormat('U', time() + 5);
        $this->setNextResponse(200, ['Expires' => $time->format(DATE_RFC2822), 'Cache-Control' => 'no-store']);

        $this->request('GET', '/');
        $this->assertTraceNotContains('store');
        $this->assertFalse($this->response->headers->has('Age'));
    }

    public function test_does_not_cache_responses_without_freshness_information_or_a_validator()
    {
        $this->setNextResponse();

        $this->request('GET', '/');
        $this->assertEquals(200, $this->response->getStatusCode());
        $this->assertTraceNotContains('store');
    }

    public function test_caches_responses_with_explicit_no_cache_directive()
    {
        $time = \DateTime::createFromFormat('U', time() + 5);
        $this->setNextResponse(200, ['Expires' => $time->format(DATE_RFC2822), 'Cache-Control' => 'public, no-cache']);

        $this->request('GET', '/');
        $this->assertTraceContains('store');
        $this->assertTrue($this->response->headers->has('Age'));
    }

    public function test_caches_responses_with_an_expiration_header()
    {
        $time = \DateTime::createFromFormat('U', time() + 5);
        $this->setNextResponse(200, ['Cache-Control' => 'public', 'Expires' => $time->format(DATE_RFC2822)]);

        $this->request('GET', '/');
        $this->assertEquals(200, $this->response->getStatusCode());
        $this->assertEquals('Hello World', $this->response->getContent());
        $this->assertNotNull($this->response->headers->get('Date'));
        $this->assertNotNull($this->response->headers->get('X-Content-Digest'));
        $this->assertTraceContains('miss');
        $this->assertTraceContains('store');

        $values = $this->getMetaStorageValues();
        $this->assertCount(1, $values);
    }

    public function test_caches_responses_with_a_max_age_directive()
    {
        $this->setNextResponse(200, ['Cache-Control' => 'public, max-age=5']);

        $this->request('GET', '/');
        $this->assertEquals(200, $this->response->getStatusCode());
        $this->assertEquals('Hello World', $this->response->getContent());
        $this->assertNotNull($this->response->headers->get('Date'));
        $this->assertNotNull($this->response->headers->get('X-Content-Digest'));
        $this->assertTraceContains('miss');
        $this->assertTraceContains('store');

        $values = $this->getMetaStorageValues();
        $this->assertCount(1, $values);
    }

    public function test_caches_responses_with_as_max_age_directive()
    {
        $this->setNextResponse(200, ['Cache-Control' => 's-maxage=5']);

        $this->request('GET', '/');
        $this->assertEquals(200, $this->response->getStatusCode());
        $this->assertEquals('Hello World', $this->response->getContent());
        $this->assertNotNull($this->response->headers->get('Date'));
        $this->assertNotNull($this->response->headers->get('X-Content-Digest'));
        $this->assertTraceContains('miss');
        $this->assertTraceContains('store');

        $values = $this->getMetaStorageValues();
        $this->assertCount(1, $values);
    }

    public function test_caches_responses_with_a_last_modified_validator_but_no_freshness_information()
    {
        $time = \DateTime::createFromFormat('U', time());
        $this->setNextResponse(200, ['Cache-Control' => 'public', 'Last-Modified' => $time->format(DATE_RFC2822)]);

        $this->request('GET', '/');
        $this->assertEquals(200, $this->response->getStatusCode());
        $this->assertEquals('Hello World', $this->response->getContent());
        $this->assertTraceContains('miss');
        $this->assertTraceContains('store');
    }

    public function test_caches_responses_with_an_e_tag_validator_but_no_freshness_information()
    {
        $this->setNextResponse(200, ['Cache-Control' => 'public', 'ETag' => '"123456"']);

        $this->request('GET', '/');
        $this->assertEquals(200, $this->response->getStatusCode());
        $this->assertEquals('Hello World', $this->response->getContent());
        $this->assertTraceContains('miss');
        $this->assertTraceContains('store');
    }

    public function test_hits_cached_responses_with_expires_header()
    {
        $time1 = \DateTime::createFromFormat('U', time() - 5);
        $time2 = \DateTime::createFromFormat('U', time() + 5);
        $this->setNextResponse(200, ['Cache-Control' => 'public', 'Date' => $time1->format(DATE_RFC2822), 'Expires' => $time2->format(DATE_RFC2822)]);

        $this->request('GET', '/');
        $this->assertHttpKernelIsCalled();
        $this->assertEquals(200, $this->response->getStatusCode());
        $this->assertNotNull($this->response->headers->get('Date'));
        $this->assertTraceContains('miss');
        $this->assertTraceContains('store');
        $this->assertEquals('Hello World', $this->response->getContent());

        $this->request('GET', '/');
        $this->assertHttpKernelIsNotCalled();
        $this->assertEquals(200, $this->response->getStatusCode());
        $this->assertTrue(strtotime($this->responses[0]->headers->get('Date')) - strtotime($this->response->headers->get('Date')) < 2);
        $this->assertTrue($this->response->headers->get('Age') > 0);
        $this->assertNotNull($this->response->headers->get('X-Content-Digest'));
        $this->assertTraceContains('fresh');
        $this->assertTraceNotContains('store');
        $this->assertEquals('Hello World', $this->response->getContent());
    }

    public function test_hits_cached_response_with_max_age_directive()
    {
        $time = \DateTime::createFromFormat('U', time() - 5);
        $this->setNextResponse(200, ['Date' => $time->format(DATE_RFC2822), 'Cache-Control' => 'public, max-age=10']);

        $this->request('GET', '/');
        $this->assertHttpKernelIsCalled();
        $this->assertEquals(200, $this->response->getStatusCode());
        $this->assertNotNull($this->response->headers->get('Date'));
        $this->assertTraceContains('miss');
        $this->assertTraceContains('store');
        $this->assertEquals('Hello World', $this->response->getContent());

        $this->request('GET', '/');
        $this->assertHttpKernelIsNotCalled();
        $this->assertEquals(200, $this->response->getStatusCode());
        $this->assertTrue(strtotime($this->responses[0]->headers->get('Date')) - strtotime($this->response->headers->get('Date')) < 2);
        $this->assertTrue($this->response->headers->get('Age') > 0);
        $this->assertNotNull($this->response->headers->get('X-Content-Digest'));
        $this->assertTraceContains('fresh');
        $this->assertTraceNotContains('store');
        $this->assertEquals('Hello World', $this->response->getContent());
    }

    public function test_hits_cached_response_with_s_max_age_directive()
    {
        $time = \DateTime::createFromFormat('U', time() - 5);
        $this->setNextResponse(200, ['Date' => $time->format(DATE_RFC2822), 'Cache-Control' => 's-maxage=10, max-age=0']);

        $this->request('GET', '/');
        $this->assertHttpKernelIsCalled();
        $this->assertEquals(200, $this->response->getStatusCode());
        $this->assertNotNull($this->response->headers->get('Date'));
        $this->assertTraceContains('miss');
        $this->assertTraceContains('store');
        $this->assertEquals('Hello World', $this->response->getContent());

        $this->request('GET', '/');
        $this->assertHttpKernelIsNotCalled();
        $this->assertEquals(200, $this->response->getStatusCode());
        $this->assertTrue(strtotime($this->responses[0]->headers->get('Date')) - strtotime($this->response->headers->get('Date')) < 2);
        $this->assertTrue($this->response->headers->get('Age') > 0);
        $this->assertNotNull($this->response->headers->get('X-Content-Digest'));
        $this->assertTraceContains('fresh');
        $this->assertTraceNotContains('store');
        $this->assertEquals('Hello World', $this->response->getContent());
    }

    public function test_assigns_default_ttl_when_response_has_no_freshness_information()
    {
        $this->setNextResponse();

        $this->cacheConfig['default_ttl'] = 10;
        $this->request('GET', '/');
        $this->assertHttpKernelIsCalled();
        $this->assertTraceContains('miss');
        $this->assertTraceContains('store');
        $this->assertEquals('Hello World', $this->response->getContent());
        $this->assertRegExp('/s-maxage=10/', $this->response->headers->get('Cache-Control'));

        $this->cacheConfig['default_ttl'] = 10;
        $this->request('GET', '/');
        $this->assertHttpKernelIsNotCalled();
        $this->assertEquals(200, $this->response->getStatusCode());
        $this->assertTraceContains('fresh');
        $this->assertTraceNotContains('store');
        $this->assertEquals('Hello World', $this->response->getContent());
        $this->assertRegExp('/s-maxage=10/', $this->response->headers->get('Cache-Control'));
    }

    public function test_assigns_default_ttl_when_response_has_no_freshness_information_and_after_ttl_was_expired()
    {
        $this->setNextResponse();

        $this->cacheConfig['default_ttl'] = 2;
        $this->request('GET', '/');
        $this->assertHttpKernelIsCalled();
        $this->assertTraceContains('miss');
        $this->assertTraceContains('store');
        $this->assertEquals('Hello World', $this->response->getContent());
        $this->assertRegExp('/s-maxage=2/', $this->response->headers->get('Cache-Control'));

        $this->request('GET', '/');
        $this->assertHttpKernelIsNotCalled();
        $this->assertEquals(200, $this->response->getStatusCode());
        $this->assertTraceContains('fresh');
        $this->assertTraceNotContains('store');
        $this->assertEquals('Hello World', $this->response->getContent());
        $this->assertRegExp('/s-maxage=2/', $this->response->headers->get('Cache-Control'));

        // expires the cache
        $values = $this->getMetaStorageValues();
        $this->assertCount(1, $values);
        $tmp = unserialize($values[0]);
        $time = \DateTime::createFromFormat('U', time() - 5);
        $tmp[0][1]['date'] = $time->format(DATE_RFC2822);
        $r = new \ReflectionObject($this->store);
        $m = $r->getMethod('save');
        $m->setAccessible(true);
        $m->invoke($this->store, 'md'.hash('sha256', 'http://localhost/'), serialize($tmp));

        $this->request('GET', '/');
        $this->assertHttpKernelIsCalled();
        $this->assertEquals(200, $this->response->getStatusCode());
        $this->assertTraceContains('stale');
        $this->assertTraceContains('invalid');
        $this->assertTraceContains('store');
        $this->assertEquals('Hello World', $this->response->getContent());
        $this->assertRegExp('/s-maxage=2/', $this->response->headers->get('Cache-Control'));

        $this->setNextResponse();

        $this->request('GET', '/');
        $this->assertHttpKernelIsNotCalled();
        $this->assertEquals(200, $this->response->getStatusCode());
        $this->assertTraceContains('fresh');
        $this->assertTraceNotContains('store');
        $this->assertEquals('Hello World', $this->response->getContent());
        $this->assertRegExp('/s-maxage=2/', $this->response->headers->get('Cache-Control'));
    }

    public function test_assigns_default_ttl_when_response_has_no_freshness_information_and_after_ttl_was_expired_with_status304()
    {
        $this->setNextResponse();

        $this->cacheConfig['default_ttl'] = 2;
        $this->request('GET', '/');
        $this->assertHttpKernelIsCalled();
        $this->assertTraceContains('miss');
        $this->assertTraceContains('store');
        $this->assertEquals('Hello World', $this->response->getContent());
        $this->assertRegExp('/s-maxage=2/', $this->response->headers->get('Cache-Control'));

        $this->request('GET', '/');
        $this->assertHttpKernelIsNotCalled();
        $this->assertEquals(200, $this->response->getStatusCode());
        $this->assertTraceContains('fresh');
        $this->assertTraceNotContains('store');
        $this->assertEquals('Hello World', $this->response->getContent());

        // expires the cache
        $values = $this->getMetaStorageValues();
        $this->assertCount(1, $values);
        $tmp = unserialize($values[0]);
        $time = \DateTime::createFromFormat('U', time() - 5);
        $tmp[0][1]['date'] = $time->format(DATE_RFC2822);
        $r = new \ReflectionObject($this->store);
        $m = $r->getMethod('save');
        $m->setAccessible(true);
        $m->invoke($this->store, 'md'.hash('sha256', 'http://localhost/'), serialize($tmp));

        $this->request('GET', '/');
        $this->assertHttpKernelIsCalled();
        $this->assertEquals(200, $this->response->getStatusCode());
        $this->assertTraceContains('stale');
        $this->assertTraceContains('valid');
        $this->assertTraceContains('store');
        $this->assertTraceNotContains('miss');
        $this->assertEquals('Hello World', $this->response->getContent());
        $this->assertRegExp('/s-maxage=2/', $this->response->headers->get('Cache-Control'));

        $this->request('GET', '/');
        $this->assertHttpKernelIsNotCalled();
        $this->assertEquals(200, $this->response->getStatusCode());
        $this->assertTraceContains('fresh');
        $this->assertTraceNotContains('store');
        $this->assertEquals('Hello World', $this->response->getContent());
        $this->assertRegExp('/s-maxage=2/', $this->response->headers->get('Cache-Control'));
    }

    public function test_does_not_assign_default_ttl_when_response_has_must_revalidate_directive()
    {
        $this->setNextResponse(200, ['Cache-Control' => 'must-revalidate']);

        $this->cacheConfig['default_ttl'] = 10;
        $this->request('GET', '/');
        $this->assertHttpKernelIsCalled();
        $this->assertEquals(200, $this->response->getStatusCode());
        $this->assertTraceContains('miss');
        $this->assertTraceNotContains('store');
        $this->assertNotRegExp('/s-maxage/', $this->response->headers->get('Cache-Control'));
        $this->assertEquals('Hello World', $this->response->getContent());
    }

    public function test_fetches_full_response_when_cache_stale_and_no_validators_present()
    {
        $time = \DateTime::createFromFormat('U', time() + 5);
        $this->setNextResponse(200, ['Cache-Control' => 'public', 'Expires' => $time->format(DATE_RFC2822)]);

        // build initial request
        $this->request('GET', '/');
        $this->assertHttpKernelIsCalled();
        $this->assertEquals(200, $this->response->getStatusCode());
        $this->assertNotNull($this->response->headers->get('Date'));
        $this->assertNotNull($this->response->headers->get('X-Content-Digest'));
        $this->assertNotNull($this->response->headers->get('Age'));
        $this->assertTraceContains('miss');
        $this->assertTraceContains('store');
        $this->assertEquals('Hello World', $this->response->getContent());

        // go in and play around with the cached metadata directly ...
        $values = $this->getMetaStorageValues();
        $this->assertCount(1, $values);
        $tmp = unserialize($values[0]);
        $time = \DateTime::createFromFormat('U', time());
        $tmp[0][1]['expires'] = $time->format(DATE_RFC2822);
        $r = new \ReflectionObject($this->store);
        $m = $r->getMethod('save');
        $m->setAccessible(true);
        $m->invoke($this->store, 'md'.hash('sha256', 'http://localhost/'), serialize($tmp));

        // build subsequent request; should be found but miss due to freshness
        $this->request('GET', '/');
        $this->assertHttpKernelIsCalled();
        $this->assertEquals(200, $this->response->getStatusCode());
        $this->assertTrue($this->response->headers->get('Age') <= 1);
        $this->assertNotNull($this->response->headers->get('X-Content-Digest'));
        $this->assertTraceContains('stale');
        $this->assertTraceNotContains('fresh');
        $this->assertTraceNotContains('miss');
        $this->assertTraceContains('store');
        $this->assertEquals('Hello World', $this->response->getContent());
    }

    public function test_validates_cached_responses_with_last_modified_and_no_freshness_information()
    {
        $time = \DateTime::createFromFormat('U', time());
        $this->setNextResponse(200, [], 'Hello World', function ($request, $response) use ($time) {
            $response->headers->set('Cache-Control', 'public');
            $response->headers->set('Last-Modified', $time->format(DATE_RFC2822));
            if ($time->format(DATE_RFC2822) == $request->headers->get('IF_MODIFIED_SINCE')) {
                $response->setStatusCode(304);
                $response->setContent('');
            }
        });

        // build initial request
        $this->request('GET', '/');
        $this->assertHttpKernelIsCalled();
        $this->assertEquals(200, $this->response->getStatusCode());
        $this->assertNotNull($this->response->headers->get('Last-Modified'));
        $this->assertNotNull($this->response->headers->get('X-Content-Digest'));
        $this->assertEquals('Hello World', $this->response->getContent());
        $this->assertTraceContains('miss');
        $this->assertTraceContains('store');
        $this->assertTraceNotContains('stale');

        // build subsequent request; should be found but miss due to freshness
        $this->request('GET', '/');
        $this->assertHttpKernelIsCalled();
        $this->assertEquals(200, $this->response->getStatusCode());
        $this->assertNotNull($this->response->headers->get('Last-Modified'));
        $this->assertNotNull($this->response->headers->get('X-Content-Digest'));
        $this->assertTrue($this->response->headers->get('Age') <= 1);
        $this->assertEquals('Hello World', $this->response->getContent());
        $this->assertTraceContains('stale');
        $this->assertTraceContains('valid');
        $this->assertTraceContains('store');
        $this->assertTraceNotContains('miss');
    }

    public function test_validates_cached_responses_with_e_tag_and_no_freshness_information()
    {
        $this->setNextResponse(200, [], 'Hello World', function ($request, $response) {
            $response->headers->set('Cache-Control', 'public');
            $response->headers->set('ETag', '"12345"');
            if ($response->getETag() == $request->headers->get('IF_NONE_MATCH')) {
                $response->setStatusCode(304);
                $response->setContent('');
            }
        });

        // build initial request
        $this->request('GET', '/');
        $this->assertHttpKernelIsCalled();
        $this->assertEquals(200, $this->response->getStatusCode());
        $this->assertNotNull($this->response->headers->get('ETag'));
        $this->assertNotNull($this->response->headers->get('X-Content-Digest'));
        $this->assertEquals('Hello World', $this->response->getContent());
        $this->assertTraceContains('miss');
        $this->assertTraceContains('store');

        // build subsequent request; should be found but miss due to freshness
        $this->request('GET', '/');
        $this->assertHttpKernelIsCalled();
        $this->assertEquals(200, $this->response->getStatusCode());
        $this->assertNotNull($this->response->headers->get('ETag'));
        $this->assertNotNull($this->response->headers->get('X-Content-Digest'));
        $this->assertTrue($this->response->headers->get('Age') <= 1);
        $this->assertEquals('Hello World', $this->response->getContent());
        $this->assertTraceContains('stale');
        $this->assertTraceContains('valid');
        $this->assertTraceContains('store');
        $this->assertTraceNotContains('miss');
    }

    public function test_replaces_cached_responses_when_validation_results_in_non304_response()
    {
        $time = \DateTime::createFromFormat('U', time());
        $count = 0;
        $this->setNextResponse(200, [], 'Hello World', function ($request, $response) use ($time, &$count) {
            $response->headers->set('Last-Modified', $time->format(DATE_RFC2822));
            $response->headers->set('Cache-Control', 'public');
            switch (++$count) {
                case 1:
                    $response->setContent('first response');
                    break;
                case 2:
                    $response->setContent('second response');
                    break;
                case 3:
                    $response->setContent('');
                    $response->setStatusCode(304);
                    break;
            }
        });

        // first request should fetch from backend and store in cache
        $this->request('GET', '/');
        $this->assertEquals(200, $this->response->getStatusCode());
        $this->assertEquals('first response', $this->response->getContent());

        // second request is validated, is invalid, and replaces cached entry
        $this->request('GET', '/');
        $this->assertEquals(200, $this->response->getStatusCode());
        $this->assertEquals('second response', $this->response->getContent());

        // third response is validated, valid, and returns cached entry
        $this->request('GET', '/');
        $this->assertEquals(200, $this->response->getStatusCode());
        $this->assertEquals('second response', $this->response->getContent());

        $this->assertEquals(3, $count);
    }

    public function test_passes_head_requests_through_directly_on_pass()
    {
        $this->setNextResponse(200, [], 'Hello World', function ($request, $response) {
            $response->setContent('');
            $response->setStatusCode(200);
            $this->assertEquals('HEAD', $request->getMethod());
        });

        $this->request('HEAD', '/', ['HTTP_EXPECT' => 'something ...']);
        $this->assertHttpKernelIsCalled();
        $this->assertEquals('', $this->response->getContent());
    }

    public function test_uses_cache_to_respond_to_head_requests_when_fresh()
    {
        $this->setNextResponse(200, [], 'Hello World', function ($request, $response) {
            $response->headers->set('Cache-Control', 'public, max-age=10');
            $response->setContent('Hello World');
            $response->setStatusCode(200);
            $this->assertNotEquals('HEAD', $request->getMethod());
        });

        $this->request('GET', '/');
        $this->assertHttpKernelIsCalled();
        $this->assertEquals('Hello World', $this->response->getContent());

        $this->request('HEAD', '/');
        $this->assertHttpKernelIsNotCalled();
        $this->assertEquals(200, $this->response->getStatusCode());
        $this->assertEquals('', $this->response->getContent());
        $this->assertEquals(strlen('Hello World'), $this->response->headers->get('Content-Length'));
    }

    public function test_sends_no_content_when_fresh()
    {
        $time = \DateTime::createFromFormat('U', time());
        $this->setNextResponse(200, [], 'Hello World', function ($request, $response) use ($time) {
            $response->headers->set('Cache-Control', 'public, max-age=10');
            $response->headers->set('Last-Modified', $time->format(DATE_RFC2822));
        });

        $this->request('GET', '/');
        $this->assertHttpKernelIsCalled();
        $this->assertEquals('Hello World', $this->response->getContent());

        $this->request('GET', '/', ['HTTP_IF_MODIFIED_SINCE' => $time->format(DATE_RFC2822)]);
        $this->assertHttpKernelIsNotCalled();
        $this->assertEquals(304, $this->response->getStatusCode());
        $this->assertEquals('', $this->response->getContent());
    }

    public function test_invalidates_cached_responses_on_post()
    {
        $this->setNextResponse(200, [], 'Hello World', function ($request, $response) {
            if ($request->getMethod() == 'GET') {
                $response->setStatusCode(200);
                $response->headers->set('Cache-Control', 'public, max-age=500');
                $response->setContent('Hello World');
            } elseif ($request->getMethod() == 'POST') {
                $response->setStatusCode(303);
                $response->headers->set('Location', '/');
                $response->headers->remove('Cache-Control');
                $response->setContent('');
            }
        });

        // build initial request to enter into the cache
        $this->request('GET', '/');
        $this->assertHttpKernelIsCalled();
        $this->assertEquals(200, $this->response->getStatusCode());
        $this->assertEquals('Hello World', $this->response->getContent());
        $this->assertTraceContains('miss');
        $this->assertTraceContains('store');

        // make sure it is valid
        $this->request('GET', '/');
        $this->assertHttpKernelIsNotCalled();
        $this->assertEquals(200, $this->response->getStatusCode());
        $this->assertEquals('Hello World', $this->response->getContent());
        $this->assertTraceContains('fresh');

        // now POST to same URL
        $this->request('POST', '/helloworld');
        $this->assertHttpKernelIsCalled();
        $this->assertEquals('/', $this->response->headers->get('Location'));
        $this->assertTraceContains('invalidate');
        $this->assertTraceContains('pass');
        $this->assertEquals('', $this->response->getContent());

        // now make sure it was actually invalidated
        $this->request('GET', '/');
        $this->assertHttpKernelIsCalled();
        $this->assertEquals(200, $this->response->getStatusCode());
        $this->assertEquals('Hello World', $this->response->getContent());
        $this->assertTraceContains('stale');
        $this->assertTraceContains('invalid');
        $this->assertTraceContains('store');
    }

    public function test_serves_from_cache_when_headers_match()
    {
        $count = 0;
        $this->setNextResponse(200, ['Cache-Control' => 'max-age=10000'], '', function ($request, $response) use (&$count) {
            $response->headers->set('Vary', 'Accept User-Agent Foo');
            $response->headers->set('Cache-Control', 'public, max-age=10');
            $response->headers->set('X-Response-Count', ++$count);
            $response->setContent($request->headers->get('USER_AGENT'));
        });

        $this->request('GET', '/', ['HTTP_ACCEPT' => 'text/html', 'HTTP_USER_AGENT' => 'Bob/1.0']);
        $this->assertEquals(200, $this->response->getStatusCode());
        $this->assertEquals('Bob/1.0', $this->response->getContent());
        $this->assertTraceContains('miss');
        $this->assertTraceContains('store');

        $this->request('GET', '/', ['HTTP_ACCEPT' => 'text/html', 'HTTP_USER_AGENT' => 'Bob/1.0']);
        $this->assertEquals(200, $this->response->getStatusCode());
        $this->assertEquals('Bob/1.0', $this->response->getContent());
        $this->assertTraceContains('fresh');
        $this->assertTraceNotContains('store');
        $this->assertNotNull($this->response->headers->get('X-Content-Digest'));
    }

    public function test_stores_multiple_responses_when_headers_differ()
    {
        $count = 0;
        $this->setNextResponse(200, ['Cache-Control' => 'max-age=10000'], '', function ($request, $response) use (&$count) {
            $response->headers->set('Vary', 'Accept User-Agent Foo');
            $response->headers->set('Cache-Control', 'public, max-age=10');
            $response->headers->set('X-Response-Count', ++$count);
            $response->setContent($request->headers->get('USER_AGENT'));
        });

        $this->request('GET', '/', ['HTTP_ACCEPT' => 'text/html', 'HTTP_USER_AGENT' => 'Bob/1.0']);
        $this->assertEquals(200, $this->response->getStatusCode());
        $this->assertEquals('Bob/1.0', $this->response->getContent());
        $this->assertEquals(1, $this->response->headers->get('X-Response-Count'));

        $this->request('GET', '/', ['HTTP_ACCEPT' => 'text/html', 'HTTP_USER_AGENT' => 'Bob/2.0']);
        $this->assertEquals(200, $this->response->getStatusCode());
        $this->assertTraceContains('miss');
        $this->assertTraceContains('store');
        $this->assertEquals('Bob/2.0', $this->response->getContent());
        $this->assertEquals(2, $this->response->headers->get('X-Response-Count'));

        $this->request('GET', '/', ['HTTP_ACCEPT' => 'text/html', 'HTTP_USER_AGENT' => 'Bob/1.0']);
        $this->assertTraceContains('fresh');
        $this->assertEquals('Bob/1.0', $this->response->getContent());
        $this->assertEquals(1, $this->response->headers->get('X-Response-Count'));

        $this->request('GET', '/', ['HTTP_ACCEPT' => 'text/html', 'HTTP_USER_AGENT' => 'Bob/2.0']);
        $this->assertTraceContains('fresh');
        $this->assertEquals('Bob/2.0', $this->response->getContent());
        $this->assertEquals(2, $this->response->headers->get('X-Response-Count'));

        $this->request('GET', '/', ['HTTP_USER_AGENT' => 'Bob/2.0']);
        $this->assertTraceContains('miss');
        $this->assertEquals('Bob/2.0', $this->response->getContent());
        $this->assertEquals(3, $this->response->headers->get('X-Response-Count'));
    }

    public function test_should_catch_exceptions()
    {
        $this->catchExceptions();

        $this->setNextResponse();
        $this->request('GET', '/');

        $this->assertExceptionsAreCaught();
    }

    public function test_should_catch_exceptions_when_reloading_and_no_cache_request()
    {
        $this->catchExceptions();

        $this->setNextResponse();
        $this->cacheConfig['allow_reload'] = true;
        $this->request('GET', '/', [], [], false, ['Pragma' => 'no-cache']);

        $this->assertExceptionsAreCaught();
    }

    public function test_should_not_catch_exceptions()
    {
        $this->catchExceptions(false);

        $this->setNextResponse();
        $this->request('GET', '/');

        $this->assertExceptionsAreNotCaught();
    }

    public function test_esi_cache_sends_the_lowest_ttl()
    {
        $responses = [
            [
                'status' => 200,
                'body' => '<esi:include src="/foo" /> <esi:include src="/bar" />',
                'headers' => [
                    'Cache-Control' => 's-maxage=300',
                    'Surrogate-Control' => 'content="ESI/1.0"',
                ],
            ],
            [
                'status' => 200,
                'body' => 'Hello World!',
                'headers' => ['Cache-Control' => 's-maxage=300'],
            ],
            [
                'status' => 200,
                'body' => 'My name is Bobby.',
                'headers' => ['Cache-Control' => 's-maxage=100'],
            ],
        ];

        $this->setNextResponses($responses);

        $this->request('GET', '/', [], [], true);
        $this->assertEquals('Hello World! My name is Bobby.', $this->response->getContent());

        // check for 100 or 99 as the test can be executed after a second change
        $this->assertTrue(in_array($this->response->getTtl(), [99, 100]));
    }

    public function test_esi_cache_force_validation()
    {
        $responses = [
            [
                'status' => 200,
                'body' => '<esi:include src="/foo" /> <esi:include src="/bar" />',
                'headers' => [
                    'Cache-Control' => 's-maxage=300',
                    'Surrogate-Control' => 'content="ESI/1.0"',
                ],
            ],
            [
                'status' => 200,
                'body' => 'Hello World!',
                'headers' => ['ETag' => 'foobar'],
            ],
            [
                'status' => 200,
                'body' => 'My name is Bobby.',
                'headers' => ['Cache-Control' => 's-maxage=100'],
            ],
        ];

        $this->setNextResponses($responses);

        $this->request('GET', '/', [], [], true);
        $this->assertEquals('Hello World! My name is Bobby.', $this->response->getContent());
        $this->assertNull($this->response->getTtl());
        $this->assertTrue($this->response->mustRevalidate());
        $this->assertTrue($this->response->headers->hasCacheControlDirective('private'));
        $this->assertTrue($this->response->headers->hasCacheControlDirective('no-cache'));
    }

    public function test_esi_recalculate_content_length_header()
    {
        $responses = [
            [
                'status' => 200,
                'body' => '<esi:include src="/foo" />',
                'headers' => [
                    'Content-Length' => 26,
                    'Cache-Control' => 's-maxage=300',
                    'Surrogate-Control' => 'content="ESI/1.0"',
                ],
            ],
            [
                'status' => 200,
                'body' => 'Hello World!',
                'headers' => [],
            ],
        ];

        $this->setNextResponses($responses);

        $this->request('GET', '/', [], [], true);
        $this->assertEquals('Hello World!', $this->response->getContent());
        $this->assertEquals(12, $this->response->headers->get('Content-Length'));
    }

    public function test_client_ip_is_always_localhost_for_forwarded_requests()
    {
        $this->setNextResponse();
        $this->request('GET', '/', ['REMOTE_ADDR' => '10.0.0.1']);

        $this->assertEquals('127.0.0.1', $this->kernel->getBackendRequest()->server->get('REMOTE_ADDR'));
    }

    /**
     * @dataProvider getTrustedProxyData
     */
    public function test_http_cache_is_set_as_a_trusted_proxy(array $existing, array $expected)
    {
        Request::setTrustedProxies($existing);

        $this->setNextResponse();
        $this->request('GET', '/', ['REMOTE_ADDR' => '10.0.0.1']);

        $this->assertEquals($expected, Request::getTrustedProxies());
    }

    public function getTrustedProxyData()
    {
        return [
            [[], ['127.0.0.1']],
            [['10.0.0.2'], ['10.0.0.2', '127.0.0.1']],
            [['10.0.0.2', '127.0.0.1'], ['10.0.0.2', '127.0.0.1']],
        ];
    }

    /**
     * @dataProvider getXForwardedForData
     */
    public function test_x_forwarder_for_header_for_forwarded_requests($xForwardedFor, $expected)
    {
        $this->setNextResponse();
        $server = ['REMOTE_ADDR' => '10.0.0.1'];
        if ($xForwardedFor !== false) {
            $server['HTTP_X_FORWARDED_FOR'] = $xForwardedFor;
        }
        $this->request('GET', '/', $server);

        $this->assertEquals($expected, $this->kernel->getBackendRequest()->headers->get('X-Forwarded-For'));
    }

    public function getXForwardedForData()
    {
        return [
            [false, '10.0.0.1'],
            ['10.0.0.2', '10.0.0.2, 10.0.0.1'],
            ['10.0.0.2, 10.0.0.3', '10.0.0.2, 10.0.0.3, 10.0.0.1'],
        ];
    }

    public function test_x_forwarder_for_header_for_pass_requests()
    {
        $this->setNextResponse();
        $server = ['REMOTE_ADDR' => '10.0.0.1'];
        $this->request('POST', '/', $server);

        $this->assertEquals('10.0.0.1', $this->kernel->getBackendRequest()->headers->get('X-Forwarded-For'));
    }

    public function test_esi_cache_remove_validation_headers_if_embedded_responses()
    {
        $time = \DateTime::createFromFormat('U', time());

        $responses = [
            [
                'status' => 200,
                'body' => '<esi:include src="/hey" />',
                'headers' => [
                    'Surrogate-Control' => 'content="ESI/1.0"',
                    'ETag' => 'hey',
                    'Last-Modified' => $time->format(DATE_RFC2822),
                ],
            ],
            [
                'status' => 200,
                'body' => 'Hey!',
                'headers' => [],
            ],
        ];

        $this->setNextResponses($responses);

        $this->request('GET', '/', [], [], true);
        $this->assertNull($this->response->getETag());
        $this->assertNull($this->response->getLastModified());
    }
}

class TestKernel implements HttpKernelInterface
{
    public $terminateCalled = false;

    public function terminate(Request $request, Response $response)
    {
        $this->terminateCalled = true;
    }

    public function handle(Request $request, $type = self::MASTER_REQUEST, $catch = true) {}
}
