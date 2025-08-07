<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\Routing\Tests\Matcher;

use Symfony\Component\Routing\RequestContext;
use Symfony\Component\Routing\Route;
use Symfony\Component\Routing\RouteCollection;

class RedirectableUrlMatcherTest extends \PHPUnit_Framework_TestCase
{
    public function test_redirect_when_no_slash()
    {
        $coll = new RouteCollection;
        $coll->add('foo', new Route('/foo/'));

        $matcher = $this->getMockForAbstractClass('Symfony\Component\Routing\Matcher\RedirectableUrlMatcher', [$coll, new RequestContext]);
        $matcher->expects($this->once())->method('redirect');
        $matcher->match('/foo');
    }

    /**
     * @expectedException \Symfony\Component\Routing\Exception\ResourceNotFoundException
     */
    public function test_redirect_when_no_slash_for_non_safe_method()
    {
        $coll = new RouteCollection;
        $coll->add('foo', new Route('/foo/'));

        $context = new RequestContext;
        $context->setMethod('POST');
        $matcher = $this->getMockForAbstractClass('Symfony\Component\Routing\Matcher\RedirectableUrlMatcher', [$coll, $context]);
        $matcher->match('/foo');
    }

    public function test_scheme_redirect_redirects_to_first_scheme()
    {
        $coll = new RouteCollection;
        $coll->add('foo', new Route('/foo', [], [], [], '', ['FTP', 'HTTPS']));

        $matcher = $this->getMockForAbstractClass('Symfony\Component\Routing\Matcher\RedirectableUrlMatcher', [$coll, new RequestContext]);
        $matcher
            ->expects($this->once())
            ->method('redirect')
            ->with('/foo', 'foo', 'ftp')
            ->will($this->returnValue(['_route' => 'foo']));
        $matcher->match('/foo');
    }

    public function test_no_schema_redirect_if_on_of_multiple_schemes_matches()
    {
        $coll = new RouteCollection;
        $coll->add('foo', new Route('/foo', [], [], [], '', ['https', 'http']));

        $matcher = $this->getMockForAbstractClass('Symfony\Component\Routing\Matcher\RedirectableUrlMatcher', [$coll, new RequestContext]);
        $matcher
            ->expects($this->never())
            ->method('redirect');
        $matcher->match('/foo');
    }
}
