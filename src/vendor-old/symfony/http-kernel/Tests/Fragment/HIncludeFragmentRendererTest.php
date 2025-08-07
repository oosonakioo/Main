<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\HttpKernel\Tests\Fragment;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Controller\ControllerReference;
use Symfony\Component\HttpKernel\Fragment\HIncludeFragmentRenderer;
use Symfony\Component\HttpKernel\UriSigner;

class HIncludeFragmentRendererTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @expectedException \LogicException
     */
    public function test_render_exception_when_controller_and_no_signer()
    {
        $strategy = new HIncludeFragmentRenderer;
        $strategy->render(new ControllerReference('main_controller', [], []), Request::create('/'));
    }

    public function test_render_with_controller_and_signer()
    {
        $strategy = new HIncludeFragmentRenderer(null, new UriSigner('foo'));

        $this->assertEquals('<hx:include src="/_fragment?_path=_format%3Dhtml%26_locale%3Den%26_controller%3Dmain_controller&amp;_hash=BP%2BOzCD5MRUI%2BHJpgPDOmoju00FnzLhP3TGcSHbbBLs%3D"></hx:include>', $strategy->render(new ControllerReference('main_controller', [], []), Request::create('/'))->getContent());
    }

    public function test_render_with_uri()
    {
        $strategy = new HIncludeFragmentRenderer;
        $this->assertEquals('<hx:include src="/foo"></hx:include>', $strategy->render('/foo', Request::create('/'))->getContent());

        $strategy = new HIncludeFragmentRenderer(null, new UriSigner('foo'));
        $this->assertEquals('<hx:include src="/foo"></hx:include>', $strategy->render('/foo', Request::create('/'))->getContent());
    }

    public function test_render_with_default()
    {
        // only default
        $strategy = new HIncludeFragmentRenderer;
        $this->assertEquals('<hx:include src="/foo">default</hx:include>', $strategy->render('/foo', Request::create('/'), ['default' => 'default'])->getContent());

        // only global default
        $strategy = new HIncludeFragmentRenderer(null, null, 'global_default');
        $this->assertEquals('<hx:include src="/foo">global_default</hx:include>', $strategy->render('/foo', Request::create('/'), [])->getContent());

        // global default and default
        $strategy = new HIncludeFragmentRenderer(null, null, 'global_default');
        $this->assertEquals('<hx:include src="/foo">default</hx:include>', $strategy->render('/foo', Request::create('/'), ['default' => 'default'])->getContent());
    }

    public function test_render_with_attributes_options()
    {
        // with id
        $strategy = new HIncludeFragmentRenderer;
        $this->assertEquals('<hx:include src="/foo" id="bar">default</hx:include>', $strategy->render('/foo', Request::create('/'), ['default' => 'default', 'id' => 'bar'])->getContent());

        // with attributes
        $strategy = new HIncludeFragmentRenderer;
        $this->assertEquals('<hx:include src="/foo" p1="v1" p2="v2">default</hx:include>', $strategy->render('/foo', Request::create('/'), ['default' => 'default', 'attributes' => ['p1' => 'v1', 'p2' => 'v2']])->getContent());

        // with id & attributes
        $strategy = new HIncludeFragmentRenderer;
        $this->assertEquals('<hx:include src="/foo" p1="v1" p2="v2" id="bar">default</hx:include>', $strategy->render('/foo', Request::create('/'), ['default' => 'default', 'id' => 'bar', 'attributes' => ['p1' => 'v1', 'p2' => 'v2']])->getContent());
    }

    public function test_render_with_default_text()
    {
        $engine = $this->getMock('Symfony\\Component\\Templating\\EngineInterface');
        $engine->expects($this->once())
            ->method('exists')
            ->with('default')
            ->will($this->throwException(new \InvalidArgumentException));

        // only default
        $strategy = new HIncludeFragmentRenderer($engine);
        $this->assertEquals('<hx:include src="/foo">default</hx:include>', $strategy->render('/foo', Request::create('/'), ['default' => 'default'])->getContent());
    }
}
