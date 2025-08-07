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
use Symfony\Component\HttpKernel\HttpCache\Esi;

class EsiTest extends \PHPUnit_Framework_TestCase
{
    public function test_has_surrogate_esi_capability()
    {
        $esi = new Esi;

        $request = Request::create('/');
        $request->headers->set('Surrogate-Capability', 'abc="ESI/1.0"');
        $this->assertTrue($esi->hasSurrogateCapability($request));

        $request = Request::create('/');
        $request->headers->set('Surrogate-Capability', 'foobar');
        $this->assertFalse($esi->hasSurrogateCapability($request));

        $request = Request::create('/');
        $this->assertFalse($esi->hasSurrogateCapability($request));
    }

    public function test_add_surrogate_esi_capability()
    {
        $esi = new Esi;

        $request = Request::create('/');
        $esi->addSurrogateCapability($request);
        $this->assertEquals('symfony2="ESI/1.0"', $request->headers->get('Surrogate-Capability'));

        $esi->addSurrogateCapability($request);
        $this->assertEquals('symfony2="ESI/1.0", symfony2="ESI/1.0"', $request->headers->get('Surrogate-Capability'));
    }

    public function test_add_surrogate_control()
    {
        $esi = new Esi;

        $response = new Response('foo <esi:include src="" />');
        $esi->addSurrogateControl($response);
        $this->assertEquals('content="ESI/1.0"', $response->headers->get('Surrogate-Control'));

        $response = new Response('foo');
        $esi->addSurrogateControl($response);
        $this->assertEquals('', $response->headers->get('Surrogate-Control'));
    }

    public function test_needs_esi_parsing()
    {
        $esi = new Esi;

        $response = new Response;
        $response->headers->set('Surrogate-Control', 'content="ESI/1.0"');
        $this->assertTrue($esi->needsParsing($response));

        $response = new Response;
        $this->assertFalse($esi->needsParsing($response));
    }

    public function test_render_include_tag()
    {
        $esi = new Esi;

        $this->assertEquals('<esi:include src="/" onerror="continue" alt="/alt" />', $esi->renderIncludeTag('/', '/alt', true));
        $this->assertEquals('<esi:include src="/" alt="/alt" />', $esi->renderIncludeTag('/', '/alt', false));
        $this->assertEquals('<esi:include src="/" onerror="continue" />', $esi->renderIncludeTag('/'));
        $this->assertEquals('<esi:comment text="some comment" />'."\n".'<esi:include src="/" onerror="continue" alt="/alt" />', $esi->renderIncludeTag('/', '/alt', true, 'some comment'));
    }

    public function test_process_does_nothing_if_content_type_is_not_html()
    {
        $esi = new Esi;

        $request = Request::create('/');
        $response = new Response;
        $response->headers->set('Content-Type', 'text/plain');
        $esi->process($request, $response);

        $this->assertFalse($response->headers->has('x-body-eval'));
    }

    public function test_multiline_esi_remove_tags_are_removed()
    {
        $esi = new Esi;

        $request = Request::create('/');
        $response = new Response('<esi:remove> <a href="http://www.example.com">www.example.com</a> </esi:remove> Keep this'."<esi:remove>\n <a>www.example.com</a> </esi:remove> And this");
        $esi->process($request, $response);

        $this->assertEquals(' Keep this And this', $response->getContent());
    }

    public function test_comment_tags_are_removed()
    {
        $esi = new Esi;

        $request = Request::create('/');
        $response = new Response('<esi:comment text="some comment &gt;" /> Keep this');
        $esi->process($request, $response);

        $this->assertEquals(' Keep this', $response->getContent());
    }

    public function test_process()
    {
        $esi = new Esi;

        $request = Request::create('/');
        $response = new Response('foo <esi:comment text="some comment" /><esi:include src="..." alt="alt" onerror="continue" />');
        $esi->process($request, $response);

        $this->assertEquals('foo <?php echo $this->surrogate->handle($this, \'...\', \'alt\', true) ?>'."\n", $response->getContent());
        $this->assertEquals('ESI', $response->headers->get('x-body-eval'));

        $response = new Response('foo <esi:comment text="some comment" /><esi:include src="foo\'" alt="bar\'" onerror="continue" />');
        $esi->process($request, $response);

        $this->assertEquals('foo <?php echo $this->surrogate->handle($this, \'foo\\\'\', \'bar\\\'\', true) ?>'."\n", $response->getContent());

        $response = new Response('foo <esi:include src="..." />');
        $esi->process($request, $response);

        $this->assertEquals('foo <?php echo $this->surrogate->handle($this, \'...\', \'\', false) ?>'."\n", $response->getContent());

        $response = new Response('foo <esi:include src="..."></esi:include>');
        $esi->process($request, $response);

        $this->assertEquals('foo <?php echo $this->surrogate->handle($this, \'...\', \'\', false) ?>'."\n", $response->getContent());
    }

    public function test_process_escapes_php_tags()
    {
        $esi = new Esi;

        $request = Request::create('/');
        $response = new Response('<?php <? <% <script language=php>');
        $esi->process($request, $response);

        $this->assertEquals('<?php echo "<?"; ?>php <?php echo "<?"; ?> <?php echo "<%"; ?> <?php echo "<s"; ?>cript language=php>', $response->getContent());
    }

    /**
     * @expectedException \RuntimeException
     */
    public function test_process_when_no_src_in_an_esi()
    {
        $esi = new Esi;

        $request = Request::create('/');
        $response = new Response('foo <esi:include />');
        $esi->process($request, $response);
    }

    public function test_process_remove_surrogate_control_header()
    {
        $esi = new Esi;

        $request = Request::create('/');
        $response = new Response('foo <esi:include src="..." />');
        $response->headers->set('Surrogate-Control', 'content="ESI/1.0"');
        $esi->process($request, $response);
        $this->assertEquals('ESI', $response->headers->get('x-body-eval'));

        $response->headers->set('Surrogate-Control', 'no-store, content="ESI/1.0"');
        $esi->process($request, $response);
        $this->assertEquals('ESI', $response->headers->get('x-body-eval'));
        $this->assertEquals('no-store', $response->headers->get('surrogate-control'));

        $response->headers->set('Surrogate-Control', 'content="ESI/1.0", no-store');
        $esi->process($request, $response);
        $this->assertEquals('ESI', $response->headers->get('x-body-eval'));
        $this->assertEquals('no-store', $response->headers->get('surrogate-control'));
    }

    public function test_handle()
    {
        $esi = new Esi;
        $cache = $this->getCache(Request::create('/'), new Response('foo'));
        $this->assertEquals('foo', $esi->handle($cache, '/', '/alt', true));
    }

    /**
     * @expectedException \RuntimeException
     */
    public function test_handle_when_response_is_not200()
    {
        $esi = new Esi;
        $response = new Response('foo');
        $response->setStatusCode(404);
        $cache = $this->getCache(Request::create('/'), $response);
        $esi->handle($cache, '/', '/alt', false);
    }

    public function test_handle_when_response_is_not200_and_errors_are_ignored()
    {
        $esi = new Esi;
        $response = new Response('foo');
        $response->setStatusCode(404);
        $cache = $this->getCache(Request::create('/'), $response);
        $this->assertEquals('', $esi->handle($cache, '/', '/alt', true));
    }

    public function test_handle_when_response_is_not200_and_alt_is_present()
    {
        $esi = new Esi;
        $response1 = new Response('foo');
        $response1->setStatusCode(404);
        $response2 = new Response('bar');
        $cache = $this->getCache(Request::create('/'), [$response1, $response2]);
        $this->assertEquals('bar', $esi->handle($cache, '/', '/alt', false));
    }

    protected function getCache($request, $response)
    {
        $cache = $this->getMock('Symfony\Component\HttpKernel\HttpCache\HttpCache', ['getRequest', 'handle'], [], '', false);
        $cache->expects($this->any())
            ->method('getRequest')
            ->will($this->returnValue($request));
        if (is_array($response)) {
            $cache->expects($this->any())
                ->method('handle')
                ->will(call_user_func_array([$this, 'onConsecutiveCalls'], $response));
        } else {
            $cache->expects($this->any())
                ->method('handle')
                ->will($this->returnValue($response));
        }

        return $cache;
    }
}
