<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\HttpKernel\Tests;

use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Symfony\Component\HttpKernel\Client;
use Symfony\Component\HttpKernel\Tests\Fixtures\TestClient;

class ClientTest extends \PHPUnit_Framework_TestCase
{
    public function test_do_request()
    {
        $client = new Client(new TestHttpKernel);

        $client->request('GET', '/');
        $this->assertEquals('Request: /', $client->getResponse()->getContent(), '->doRequest() uses the request handler to make the request');
        $this->assertInstanceOf('Symfony\Component\BrowserKit\Request', $client->getInternalRequest());
        $this->assertInstanceOf('Symfony\Component\HttpFoundation\Request', $client->getRequest());
        $this->assertInstanceOf('Symfony\Component\BrowserKit\Response', $client->getInternalResponse());
        $this->assertInstanceOf('Symfony\Component\HttpFoundation\Response', $client->getResponse());

        $client->request('GET', 'http://www.example.com/');
        $this->assertEquals('Request: /', $client->getResponse()->getContent(), '->doRequest() uses the request handler to make the request');
        $this->assertEquals('www.example.com', $client->getRequest()->getHost(), '->doRequest() uses the request handler to make the request');

        $client->request('GET', 'http://www.example.com/?parameter=http://google.com');
        $this->assertEquals('http://www.example.com/?parameter='.urlencode('http://google.com'), $client->getRequest()->getUri(), '->doRequest() uses the request handler to make the request');
    }

    public function test_get_script()
    {
        $client = new TestClient(new TestHttpKernel);
        $client->insulate();
        $client->request('GET', '/');

        $this->assertEquals('Request: /', $client->getResponse()->getContent(), '->getScript() returns a script that uses the request handler to make the request');
    }

    public function test_filter_response_converts_cookies()
    {
        $client = new Client(new TestHttpKernel);

        $r = new \ReflectionObject($client);
        $m = $r->getMethod('filterResponse');
        $m->setAccessible(true);

        $expected = [
            'foo=bar; expires=Sun, 15 Feb 2009 20:00:00 GMT; domain=http://example.com; path=/foo; secure; httponly',
            'foo1=bar1; expires=Sun, 15 Feb 2009 20:00:00 GMT; domain=http://example.com; path=/foo; secure; httponly',
        ];

        $response = new Response;
        $response->headers->setCookie(new Cookie('foo', 'bar', \DateTime::createFromFormat('j-M-Y H:i:s T', '15-Feb-2009 20:00:00 GMT')->format('U'), '/foo', 'http://example.com', true, true));
        $domResponse = $m->invoke($client, $response);
        $this->assertEquals($expected[0], $domResponse->getHeader('Set-Cookie'));

        $response = new Response;
        $response->headers->setCookie(new Cookie('foo', 'bar', \DateTime::createFromFormat('j-M-Y H:i:s T', '15-Feb-2009 20:00:00 GMT')->format('U'), '/foo', 'http://example.com', true, true));
        $response->headers->setCookie(new Cookie('foo1', 'bar1', \DateTime::createFromFormat('j-M-Y H:i:s T', '15-Feb-2009 20:00:00 GMT')->format('U'), '/foo', 'http://example.com', true, true));
        $domResponse = $m->invoke($client, $response);
        $this->assertEquals($expected[0], $domResponse->getHeader('Set-Cookie'));
        $this->assertEquals($expected, $domResponse->getHeader('Set-Cookie', false));
    }

    public function test_filter_response_supports_streamed_responses()
    {
        $client = new Client(new TestHttpKernel);

        $r = new \ReflectionObject($client);
        $m = $r->getMethod('filterResponse');
        $m->setAccessible(true);

        $response = new StreamedResponse(function () {
            echo 'foo';
        });

        $domResponse = $m->invoke($client, $response);
        $this->assertEquals('foo', $domResponse->getContent());
    }

    public function test_uploaded_file()
    {
        $source = tempnam(sys_get_temp_dir(), 'source');
        $target = sys_get_temp_dir().'/sf.moved.file';
        @unlink($target);

        $kernel = new TestHttpKernel;
        $client = new Client($kernel);

        $files = [
            ['tmp_name' => $source, 'name' => 'original', 'type' => 'mime/original', 'size' => 123, 'error' => UPLOAD_ERR_OK],
            new UploadedFile($source, 'original', 'mime/original', 123, UPLOAD_ERR_OK, true),
        ];

        $file = null;
        foreach ($files as $file) {
            $client->request('POST', '/', [], ['foo' => $file]);

            $files = $client->getRequest()->files->all();

            $this->assertCount(1, $files);

            $file = $files['foo'];

            $this->assertEquals('original', $file->getClientOriginalName());
            $this->assertEquals('mime/original', $file->getClientMimeType());
            $this->assertEquals('123', $file->getClientSize());
            $this->assertTrue($file->isValid());
        }

        $file->move(dirname($target), basename($target));

        $this->assertFileExists($target);
        unlink($target);
    }

    public function test_uploaded_file_when_no_file_selected()
    {
        $kernel = new TestHttpKernel;
        $client = new Client($kernel);

        $file = ['tmp_name' => '', 'name' => '', 'type' => '', 'size' => 0, 'error' => UPLOAD_ERR_NO_FILE];

        $client->request('POST', '/', [], ['foo' => $file]);

        $files = $client->getRequest()->files->all();

        $this->assertCount(1, $files);
        $this->assertNull($files['foo']);
    }

    public function test_uploaded_file_when_size_exceeds_upload_max_file_size()
    {
        $source = tempnam(sys_get_temp_dir(), 'source');

        $kernel = new TestHttpKernel;
        $client = new Client($kernel);

        $file = $this
            ->getMockBuilder('Symfony\Component\HttpFoundation\File\UploadedFile')
            ->setConstructorArgs([$source, 'original', 'mime/original', 123, UPLOAD_ERR_OK, true])
            ->setMethods(['getSize'])
            ->getMock();

        $file->expects($this->once())
            ->method('getSize')
            ->will($this->returnValue(INF));

        $client->request('POST', '/', [], [$file]);

        $files = $client->getRequest()->files->all();

        $this->assertCount(1, $files);

        $file = $files[0];

        $this->assertFalse($file->isValid());
        $this->assertEquals(UPLOAD_ERR_INI_SIZE, $file->getError());
        $this->assertEquals('mime/original', $file->getClientMimeType());
        $this->assertEquals('original', $file->getClientOriginalName());
        $this->assertEquals(0, $file->getClientSize());

        unlink($source);
    }
}
