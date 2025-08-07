<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\HttpFoundation\Tests;

use Symfony\Component\HttpFoundation\RedirectResponse;

class RedirectResponseTest extends \PHPUnit_Framework_TestCase
{
    public function test_generate_meta_redirect()
    {
        $response = new RedirectResponse('foo.bar');

        $this->assertEquals(1, preg_match(
            '#<meta http-equiv="refresh" content="\d+;url=foo\.bar" />#',
            preg_replace(['/\s+/', '/\'/'], [' ', '"'], $response->getContent())
        ));
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function test_redirect_response_constructor_null_url()
    {
        $response = new RedirectResponse(null);
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function test_redirect_response_constructor_wrong_status_code()
    {
        $response = new RedirectResponse('foo.bar', 404);
    }

    public function test_generate_location_header()
    {
        $response = new RedirectResponse('foo.bar');

        $this->assertTrue($response->headers->has('Location'));
        $this->assertEquals('foo.bar', $response->headers->get('Location'));
    }

    public function test_get_target_url()
    {
        $response = new RedirectResponse('foo.bar');

        $this->assertEquals('foo.bar', $response->getTargetUrl());
    }

    public function test_set_target_url()
    {
        $response = new RedirectResponse('foo.bar');
        $response->setTargetUrl('baz.beep');

        $this->assertEquals('baz.beep', $response->getTargetUrl());
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function test_set_target_url_null()
    {
        $response = new RedirectResponse('foo.bar');
        $response->setTargetUrl(null);
    }

    public function test_create()
    {
        $response = RedirectResponse::create('foo', 301);

        $this->assertInstanceOf('Symfony\Component\HttpFoundation\RedirectResponse', $response);
        $this->assertEquals(301, $response->getStatusCode());
    }
}
