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

use Symfony\Component\Routing\Exception\MethodNotAllowedException;
use Symfony\Component\Routing\Exception\ResourceNotFoundException;
use Symfony\Component\Routing\Matcher\UrlMatcher;
use Symfony\Component\Routing\RequestContext;
use Symfony\Component\Routing\Route;
use Symfony\Component\Routing\RouteCollection;

class UrlMatcherTest extends \PHPUnit_Framework_TestCase
{
    public function test_no_method_so_allowed()
    {
        $coll = new RouteCollection;
        $coll->add('foo', new Route('/foo'));

        $matcher = new UrlMatcher($coll, new RequestContext);
        $this->assertInternalType('array', $matcher->match('/foo'));
    }

    public function test_method_not_allowed()
    {
        $coll = new RouteCollection;
        $coll->add('foo', new Route('/foo', [], [], [], '', [], ['post']));

        $matcher = new UrlMatcher($coll, new RequestContext);

        try {
            $matcher->match('/foo');
            $this->fail();
        } catch (MethodNotAllowedException $e) {
            $this->assertEquals(['POST'], $e->getAllowedMethods());
        }
    }

    public function test_head_allowed_when_requirement_contains_get()
    {
        $coll = new RouteCollection;
        $coll->add('foo', new Route('/foo', [], [], [], '', [], ['get']));

        $matcher = new UrlMatcher($coll, new RequestContext('', 'head'));
        $this->assertInternalType('array', $matcher->match('/foo'));
    }

    public function test_method_not_allowed_aggregates_allowed_methods()
    {
        $coll = new RouteCollection;
        $coll->add('foo1', new Route('/foo', [], [], [], '', [], ['post']));
        $coll->add('foo2', new Route('/foo', [], [], [], '', [], ['put', 'delete']));

        $matcher = new UrlMatcher($coll, new RequestContext);

        try {
            $matcher->match('/foo');
            $this->fail();
        } catch (MethodNotAllowedException $e) {
            $this->assertEquals(['POST', 'PUT', 'DELETE'], $e->getAllowedMethods());
        }
    }

    public function test_match()
    {
        // test the patterns are matched and parameters are returned
        $collection = new RouteCollection;
        $collection->add('foo', new Route('/foo/{bar}'));
        $matcher = new UrlMatcher($collection, new RequestContext);
        try {
            $matcher->match('/no-match');
            $this->fail();
        } catch (ResourceNotFoundException $e) {
        }
        $this->assertEquals(['_route' => 'foo', 'bar' => 'baz'], $matcher->match('/foo/baz'));

        // test that defaults are merged
        $collection = new RouteCollection;
        $collection->add('foo', new Route('/foo/{bar}', ['def' => 'test']));
        $matcher = new UrlMatcher($collection, new RequestContext);
        $this->assertEquals(['_route' => 'foo', 'bar' => 'baz', 'def' => 'test'], $matcher->match('/foo/baz'));

        // test that route "method" is ignored if no method is given in the context
        $collection = new RouteCollection;
        $collection->add('foo', new Route('/foo', [], [], [], '', [], ['get', 'head']));
        $matcher = new UrlMatcher($collection, new RequestContext);
        $this->assertInternalType('array', $matcher->match('/foo'));

        // route does not match with POST method context
        $matcher = new UrlMatcher($collection, new RequestContext('', 'post'));
        try {
            $matcher->match('/foo');
            $this->fail();
        } catch (MethodNotAllowedException $e) {
        }

        // route does match with GET or HEAD method context
        $matcher = new UrlMatcher($collection, new RequestContext);
        $this->assertInternalType('array', $matcher->match('/foo'));
        $matcher = new UrlMatcher($collection, new RequestContext('', 'head'));
        $this->assertInternalType('array', $matcher->match('/foo'));

        // route with an optional variable as the first segment
        $collection = new RouteCollection;
        $collection->add('bar', new Route('/{bar}/foo', ['bar' => 'bar'], ['bar' => 'foo|bar']));
        $matcher = new UrlMatcher($collection, new RequestContext);
        $this->assertEquals(['_route' => 'bar', 'bar' => 'bar'], $matcher->match('/bar/foo'));
        $this->assertEquals(['_route' => 'bar', 'bar' => 'foo'], $matcher->match('/foo/foo'));

        $collection = new RouteCollection;
        $collection->add('bar', new Route('/{bar}', ['bar' => 'bar'], ['bar' => 'foo|bar']));
        $matcher = new UrlMatcher($collection, new RequestContext);
        $this->assertEquals(['_route' => 'bar', 'bar' => 'foo'], $matcher->match('/foo'));
        $this->assertEquals(['_route' => 'bar', 'bar' => 'bar'], $matcher->match('/'));

        // route with only optional variables
        $collection = new RouteCollection;
        $collection->add('bar', new Route('/{foo}/{bar}', ['foo' => 'foo', 'bar' => 'bar'], []));
        $matcher = new UrlMatcher($collection, new RequestContext);
        $this->assertEquals(['_route' => 'bar', 'foo' => 'foo', 'bar' => 'bar'], $matcher->match('/'));
        $this->assertEquals(['_route' => 'bar', 'foo' => 'a', 'bar' => 'bar'], $matcher->match('/a'));
        $this->assertEquals(['_route' => 'bar', 'foo' => 'a', 'bar' => 'b'], $matcher->match('/a/b'));
    }

    public function test_match_with_prefixes()
    {
        $collection = new RouteCollection;
        $collection->add('foo', new Route('/{foo}'));
        $collection->addPrefix('/b');
        $collection->addPrefix('/a');

        $matcher = new UrlMatcher($collection, new RequestContext);
        $this->assertEquals(['_route' => 'foo', 'foo' => 'foo'], $matcher->match('/a/b/foo'));
    }

    public function test_match_with_dynamic_prefix()
    {
        $collection = new RouteCollection;
        $collection->add('foo', new Route('/{foo}'));
        $collection->addPrefix('/b');
        $collection->addPrefix('/{_locale}');

        $matcher = new UrlMatcher($collection, new RequestContext);
        $this->assertEquals(['_locale' => 'fr', '_route' => 'foo', 'foo' => 'foo'], $matcher->match('/fr/b/foo'));
    }

    public function test_match_special_route_name()
    {
        $collection = new RouteCollection;
        $collection->add('$péß^a|', new Route('/bar'));

        $matcher = new UrlMatcher($collection, new RequestContext);
        $this->assertEquals(['_route' => '$péß^a|'], $matcher->match('/bar'));
    }

    public function test_match_non_alpha()
    {
        $collection = new RouteCollection;
        $chars = '!"$%éà &\'()*+,./:;<=>@ABCDEFGHIJKLMNOPQRSTUVWXYZ\\[]^_`abcdefghijklmnopqrstuvwxyz{|}~-';
        $collection->add('foo', new Route('/{foo}/bar', [], ['foo' => '['.preg_quote($chars).']+']));

        $matcher = new UrlMatcher($collection, new RequestContext);
        $this->assertEquals(['_route' => 'foo', 'foo' => $chars], $matcher->match('/'.rawurlencode($chars).'/bar'));
        $this->assertEquals(['_route' => 'foo', 'foo' => $chars], $matcher->match('/'.strtr($chars, ['%' => '%25']).'/bar'));
    }

    public function test_match_with_dot_metacharacter_in_requirements()
    {
        $collection = new RouteCollection;
        $collection->add('foo', new Route('/{foo}/bar', [], ['foo' => '.+']));

        $matcher = new UrlMatcher($collection, new RequestContext);
        $this->assertEquals(['_route' => 'foo', 'foo' => "\n"], $matcher->match('/'.urlencode("\n").'/bar'), 'linefeed character is matched');
    }

    public function test_match_overridden_route()
    {
        $collection = new RouteCollection;
        $collection->add('foo', new Route('/foo'));

        $collection1 = new RouteCollection;
        $collection1->add('foo', new Route('/foo1'));

        $collection->addCollection($collection1);

        $matcher = new UrlMatcher($collection, new RequestContext);

        $this->assertEquals(['_route' => 'foo'], $matcher->match('/foo1'));
        $this->setExpectedException('Symfony\Component\Routing\Exception\ResourceNotFoundException');
        $this->assertEquals([], $matcher->match('/foo'));
    }

    public function test_match_regression()
    {
        $coll = new RouteCollection;
        $coll->add('foo', new Route('/foo/{foo}'));
        $coll->add('bar', new Route('/foo/bar/{foo}'));

        $matcher = new UrlMatcher($coll, new RequestContext);
        $this->assertEquals(['foo' => 'bar', '_route' => 'bar'], $matcher->match('/foo/bar/bar'));

        $collection = new RouteCollection;
        $collection->add('foo', new Route('/{bar}'));
        $matcher = new UrlMatcher($collection, new RequestContext);
        try {
            $matcher->match('/');
            $this->fail();
        } catch (ResourceNotFoundException $e) {
        }
    }

    public function test_default_requirement_for_optional_variables()
    {
        $coll = new RouteCollection;
        $coll->add('test', new Route('/{page}.{_format}', ['page' => 'index', '_format' => 'html']));

        $matcher = new UrlMatcher($coll, new RequestContext);
        $this->assertEquals(['page' => 'my-page', '_format' => 'xml', '_route' => 'test'], $matcher->match('/my-page.xml'));
    }

    public function test_matching_is_eager()
    {
        $coll = new RouteCollection;
        $coll->add('test', new Route('/{foo}-{bar}-', [], ['foo' => '.+', 'bar' => '.+']));

        $matcher = new UrlMatcher($coll, new RequestContext);
        $this->assertEquals(['foo' => 'text1-text2-text3', 'bar' => 'text4', '_route' => 'test'], $matcher->match('/text1-text2-text3-text4-'));
    }

    public function test_adjacent_variables()
    {
        $coll = new RouteCollection;
        $coll->add('test', new Route('/{w}{x}{y}{z}.{_format}', ['z' => 'default-z', '_format' => 'html'], ['y' => 'y|Y']));

        $matcher = new UrlMatcher($coll, new RequestContext);
        // 'w' eagerly matches as much as possible and the other variables match the remaining chars.
        // This also shows that the variables w-z must all exclude the separating char (the dot '.' in this case) by default requirement.
        // Otherwise they would also consume '.xml' and _format would never match as it's an optional variable.
        $this->assertEquals(['w' => 'wwwww', 'x' => 'x', 'y' => 'Y', 'z' => 'Z', '_format' => 'xml', '_route' => 'test'], $matcher->match('/wwwwwxYZ.xml'));
        // As 'y' has custom requirement and can only be of value 'y|Y', it will leave  'ZZZ' to variable z.
        // So with carefully chosen requirements adjacent variables, can be useful.
        $this->assertEquals(['w' => 'wwwww', 'x' => 'x', 'y' => 'y', 'z' => 'ZZZ', '_format' => 'html', '_route' => 'test'], $matcher->match('/wwwwwxyZZZ'));
        // z and _format are optional.
        $this->assertEquals(['w' => 'wwwww', 'x' => 'x', 'y' => 'y', 'z' => 'default-z', '_format' => 'html', '_route' => 'test'], $matcher->match('/wwwwwxy'));

        $this->setExpectedException('Symfony\Component\Routing\Exception\ResourceNotFoundException');
        $matcher->match('/wxy.html');
    }

    public function test_optional_variable_with_no_real_separator()
    {
        $coll = new RouteCollection;
        $coll->add('test', new Route('/get{what}', ['what' => 'All']));
        $matcher = new UrlMatcher($coll, new RequestContext);

        $this->assertEquals(['what' => 'All', '_route' => 'test'], $matcher->match('/get'));
        $this->assertEquals(['what' => 'Sites', '_route' => 'test'], $matcher->match('/getSites'));

        // Usually the character in front of an optional parameter can be left out, e.g. with pattern '/get/{what}' just '/get' would match.
        // But here the 't' in 'get' is not a separating character, so it makes no sense to match without it.
        $this->setExpectedException('Symfony\Component\Routing\Exception\ResourceNotFoundException');
        $matcher->match('/ge');
    }

    public function test_required_variable_with_no_real_separator()
    {
        $coll = new RouteCollection;
        $coll->add('test', new Route('/get{what}Suffix'));
        $matcher = new UrlMatcher($coll, new RequestContext);

        $this->assertEquals(['what' => 'Sites', '_route' => 'test'], $matcher->match('/getSitesSuffix'));
    }

    public function test_default_requirement_of_variable()
    {
        $coll = new RouteCollection;
        $coll->add('test', new Route('/{page}.{_format}'));
        $matcher = new UrlMatcher($coll, new RequestContext);

        $this->assertEquals(['page' => 'index', '_format' => 'mobile.html', '_route' => 'test'], $matcher->match('/index.mobile.html'));
    }

    /**
     * @expectedException \Symfony\Component\Routing\Exception\ResourceNotFoundException
     */
    public function test_default_requirement_of_variable_disallows_slash()
    {
        $coll = new RouteCollection;
        $coll->add('test', new Route('/{page}.{_format}'));
        $matcher = new UrlMatcher($coll, new RequestContext);

        $matcher->match('/index.sl/ash');
    }

    /**
     * @expectedException \Symfony\Component\Routing\Exception\ResourceNotFoundException
     */
    public function test_default_requirement_of_variable_disallows_next_separator()
    {
        $coll = new RouteCollection;
        $coll->add('test', new Route('/{page}.{_format}', [], ['_format' => 'html|xml']));
        $matcher = new UrlMatcher($coll, new RequestContext);

        $matcher->match('/do.t.html');
    }

    /**
     * @expectedException \Symfony\Component\Routing\Exception\ResourceNotFoundException
     */
    public function test_scheme_requirement()
    {
        $coll = new RouteCollection;
        $coll->add('foo', new Route('/foo', [], [], [], '', ['https']));
        $matcher = new UrlMatcher($coll, new RequestContext);
        $matcher->match('/foo');
    }

    /**
     * @expectedException \Symfony\Component\Routing\Exception\ResourceNotFoundException
     */
    public function test_condition()
    {
        $coll = new RouteCollection;
        $route = new Route('/foo');
        $route->setCondition('context.getMethod() == "POST"');
        $coll->add('foo', $route);
        $matcher = new UrlMatcher($coll, new RequestContext);
        $matcher->match('/foo');
    }

    public function test_decode_once()
    {
        $coll = new RouteCollection;
        $coll->add('foo', new Route('/foo/{foo}'));

        $matcher = new UrlMatcher($coll, new RequestContext);
        $this->assertEquals(['foo' => 'bar%23', '_route' => 'foo'], $matcher->match('/foo/bar%2523'));
    }

    public function test_cannot_rely_on_prefix()
    {
        $coll = new RouteCollection;

        $subColl = new RouteCollection;
        $subColl->add('bar', new Route('/bar'));
        $subColl->addPrefix('/prefix');
        // overwrite the pattern, so the prefix is not valid anymore for this route in the collection
        $subColl->get('bar')->setPath('/new');

        $coll->addCollection($subColl);

        $matcher = new UrlMatcher($coll, new RequestContext);
        $this->assertEquals(['_route' => 'bar'], $matcher->match('/new'));
    }

    public function test_with_host()
    {
        $coll = new RouteCollection;
        $coll->add('foo', new Route('/foo/{foo}', [], [], [], '{locale}.example.com'));

        $matcher = new UrlMatcher($coll, new RequestContext('', 'GET', 'en.example.com'));
        $this->assertEquals(['foo' => 'bar', '_route' => 'foo', 'locale' => 'en'], $matcher->match('/foo/bar'));
    }

    public function test_with_host_on_route_collection()
    {
        $coll = new RouteCollection;
        $coll->add('foo', new Route('/foo/{foo}'));
        $coll->add('bar', new Route('/bar/{foo}', [], [], [], '{locale}.example.net'));
        $coll->setHost('{locale}.example.com');

        $matcher = new UrlMatcher($coll, new RequestContext('', 'GET', 'en.example.com'));
        $this->assertEquals(['foo' => 'bar', '_route' => 'foo', 'locale' => 'en'], $matcher->match('/foo/bar'));

        $matcher = new UrlMatcher($coll, new RequestContext('', 'GET', 'en.example.com'));
        $this->assertEquals(['foo' => 'bar', '_route' => 'bar', 'locale' => 'en'], $matcher->match('/bar/bar'));
    }

    /**
     * @expectedException \Symfony\Component\Routing\Exception\ResourceNotFoundException
     */
    public function test_with_out_host_host_does_not_match()
    {
        $coll = new RouteCollection;
        $coll->add('foo', new Route('/foo/{foo}', [], [], [], '{locale}.example.com'));

        $matcher = new UrlMatcher($coll, new RequestContext('', 'GET', 'example.com'));
        $matcher->match('/foo/bar');
    }

    /**
     * @expectedException \Symfony\Component\Routing\Exception\ResourceNotFoundException
     */
    public function test_path_is_case_sensitive()
    {
        $coll = new RouteCollection;
        $coll->add('foo', new Route('/locale', [], ['locale' => 'EN|FR|DE']));

        $matcher = new UrlMatcher($coll, new RequestContext);
        $matcher->match('/en');
    }

    public function test_host_is_case_insensitive()
    {
        $coll = new RouteCollection;
        $coll->add('foo', new Route('/', [], ['locale' => 'EN|FR|DE'], [], '{locale}.example.com'));

        $matcher = new UrlMatcher($coll, new RequestContext('', 'GET', 'en.example.com'));
        $this->assertEquals(['_route' => 'foo', 'locale' => 'en'], $matcher->match('/'));
    }
}
