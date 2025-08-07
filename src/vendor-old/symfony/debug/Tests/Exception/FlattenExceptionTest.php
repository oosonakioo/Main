<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\Debug\Tests\Exception;

use Symfony\Component\Debug\Exception\FlattenException;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\ConflictHttpException;
use Symfony\Component\HttpKernel\Exception\GoneHttpException;
use Symfony\Component\HttpKernel\Exception\LengthRequiredHttpException;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Symfony\Component\HttpKernel\Exception\NotAcceptableHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\PreconditionFailedHttpException;
use Symfony\Component\HttpKernel\Exception\PreconditionRequiredHttpException;
use Symfony\Component\HttpKernel\Exception\ServiceUnavailableHttpException;
use Symfony\Component\HttpKernel\Exception\TooManyRequestsHttpException;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;
use Symfony\Component\HttpKernel\Exception\UnsupportedMediaTypeHttpException;

class FlattenExceptionTest extends \PHPUnit_Framework_TestCase
{
    public function test_status_code()
    {
        $flattened = FlattenException::create(new \RuntimeException, 403);
        $this->assertEquals('403', $flattened->getStatusCode());

        $flattened = FlattenException::create(new \RuntimeException);
        $this->assertEquals('500', $flattened->getStatusCode());

        $flattened = FlattenException::create(new NotFoundHttpException);
        $this->assertEquals('404', $flattened->getStatusCode());

        $flattened = FlattenException::create(new UnauthorizedHttpException('Basic realm="My Realm"'));
        $this->assertEquals('401', $flattened->getStatusCode());

        $flattened = FlattenException::create(new BadRequestHttpException);
        $this->assertEquals('400', $flattened->getStatusCode());

        $flattened = FlattenException::create(new NotAcceptableHttpException);
        $this->assertEquals('406', $flattened->getStatusCode());

        $flattened = FlattenException::create(new ConflictHttpException);
        $this->assertEquals('409', $flattened->getStatusCode());

        $flattened = FlattenException::create(new MethodNotAllowedHttpException(['POST']));
        $this->assertEquals('405', $flattened->getStatusCode());

        $flattened = FlattenException::create(new AccessDeniedHttpException);
        $this->assertEquals('403', $flattened->getStatusCode());

        $flattened = FlattenException::create(new GoneHttpException);
        $this->assertEquals('410', $flattened->getStatusCode());

        $flattened = FlattenException::create(new LengthRequiredHttpException);
        $this->assertEquals('411', $flattened->getStatusCode());

        $flattened = FlattenException::create(new PreconditionFailedHttpException);
        $this->assertEquals('412', $flattened->getStatusCode());

        $flattened = FlattenException::create(new PreconditionRequiredHttpException);
        $this->assertEquals('428', $flattened->getStatusCode());

        $flattened = FlattenException::create(new ServiceUnavailableHttpException);
        $this->assertEquals('503', $flattened->getStatusCode());

        $flattened = FlattenException::create(new TooManyRequestsHttpException);
        $this->assertEquals('429', $flattened->getStatusCode());

        $flattened = FlattenException::create(new UnsupportedMediaTypeHttpException);
        $this->assertEquals('415', $flattened->getStatusCode());
    }

    public function test_headers_for_http_exception()
    {
        $flattened = FlattenException::create(new MethodNotAllowedHttpException(['POST']));
        $this->assertEquals(['Allow' => 'POST'], $flattened->getHeaders());

        $flattened = FlattenException::create(new UnauthorizedHttpException('Basic realm="My Realm"'));
        $this->assertEquals(['WWW-Authenticate' => 'Basic realm="My Realm"'], $flattened->getHeaders());

        $flattened = FlattenException::create(new ServiceUnavailableHttpException('Fri, 31 Dec 1999 23:59:59 GMT'));
        $this->assertEquals(['Retry-After' => 'Fri, 31 Dec 1999 23:59:59 GMT'], $flattened->getHeaders());

        $flattened = FlattenException::create(new ServiceUnavailableHttpException(120));
        $this->assertEquals(['Retry-After' => 120], $flattened->getHeaders());

        $flattened = FlattenException::create(new TooManyRequestsHttpException('Fri, 31 Dec 1999 23:59:59 GMT'));
        $this->assertEquals(['Retry-After' => 'Fri, 31 Dec 1999 23:59:59 GMT'], $flattened->getHeaders());

        $flattened = FlattenException::create(new TooManyRequestsHttpException(120));
        $this->assertEquals(['Retry-After' => 120], $flattened->getHeaders());
    }

    /**
     * @dataProvider flattenDataProvider
     */
    public function test_flatten_http_exception(\Exception $exception, $statusCode)
    {
        $flattened = FlattenException::create($exception);
        $flattened2 = FlattenException::create($exception);

        $flattened->setPrevious($flattened2);

        $this->assertEquals($exception->getMessage(), $flattened->getMessage(), 'The message is copied from the original exception.');
        $this->assertEquals($exception->getCode(), $flattened->getCode(), 'The code is copied from the original exception.');
        $this->assertInstanceOf($flattened->getClass(), $exception, 'The class is set to the class of the original exception');
    }

    /**
     * @dataProvider flattenDataProvider
     */
    public function test_previous(\Exception $exception, $statusCode)
    {
        $flattened = FlattenException::create($exception);
        $flattened2 = FlattenException::create($exception);

        $flattened->setPrevious($flattened2);

        $this->assertSame($flattened2, $flattened->getPrevious());

        $this->assertSame([$flattened2], $flattened->getAllPrevious());
    }

    /**
     * @requires PHP 7.0
     */
    public function test_previous_error()
    {
        $exception = new \Exception('test', 123, new \ParseError('Oh noes!', 42));

        $flattened = FlattenException::create($exception)->getPrevious();

        $this->assertEquals($flattened->getMessage(), 'Parse error: Oh noes!', 'The message is copied from the original exception.');
        $this->assertEquals($flattened->getCode(), 42, 'The code is copied from the original exception.');
        $this->assertEquals($flattened->getClass(), 'Symfony\Component\Debug\Exception\FatalThrowableError', 'The class is set to the class of the original exception');
    }

    /**
     * @dataProvider flattenDataProvider
     */
    public function test_line(\Exception $exception)
    {
        $flattened = FlattenException::create($exception);
        $this->assertSame($exception->getLine(), $flattened->getLine());
    }

    /**
     * @dataProvider flattenDataProvider
     */
    public function test_file(\Exception $exception)
    {
        $flattened = FlattenException::create($exception);
        $this->assertSame($exception->getFile(), $flattened->getFile());
    }

    /**
     * @dataProvider flattenDataProvider
     */
    public function test_to_array(\Exception $exception, $statusCode)
    {
        $flattened = FlattenException::create($exception);
        $flattened->setTrace([], 'foo.php', 123);

        $this->assertEquals([
            [
                'message' => 'test',
                'class' => 'Exception',
                'trace' => [[
                    'namespace' => '', 'short_class' => '', 'class' => '', 'type' => '', 'function' => '', 'file' => 'foo.php', 'line' => 123,
                    'args' => [],
                ]],
            ],
        ], $flattened->toArray());
    }

    public function flattenDataProvider()
    {
        return [
            [new \Exception('test', 123), 500],
        ];
    }

    public function test_recursion_in_arguments()
    {
        $a = ['foo', [2, &$a]];
        $exception = $this->createException($a);

        $flattened = FlattenException::create($exception);
        $trace = $flattened->getTrace();
        $this->assertContains('*DEEP NESTED ARRAY*', serialize($trace));
    }

    public function test_too_big_array()
    {
        $a = [];
        for ($i = 0; $i < 20; $i++) {
            for ($j = 0; $j < 50; $j++) {
                for ($k = 0; $k < 10; $k++) {
                    $a[$i][$j][$k] = 'value';
                }
            }
        }
        $a[20] = 'value';
        $a[21] = 'value1';
        $exception = $this->createException($a);

        $flattened = FlattenException::create($exception);
        $trace = $flattened->getTrace();
        $serializeTrace = serialize($trace);

        $this->assertContains('*SKIPPED over 10000 entries*', $serializeTrace);
        $this->assertNotContains('*value1*', $serializeTrace);
    }

    private function createException($foo)
    {
        return new \Exception;
    }

    public function test_set_trace_incomplete_class()
    {
        $flattened = FlattenException::create(new \Exception('test', 123));
        $flattened->setTrace(
            [
                [
                    'file' => __FILE__,
                    'line' => 123,
                    'function' => 'test',
                    'args' => [
                        unserialize('O:14:"BogusTestClass":0:{}'),
                    ],
                ],
            ],
            'foo.php', 123
        );

        $this->assertEquals([
            [
                'message' => 'test',
                'class' => 'Exception',
                'trace' => [
                    [
                        'namespace' => '', 'short_class' => '', 'class' => '', 'type' => '', 'function' => '',
                        'file' => 'foo.php', 'line' => 123,
                        'args' => [],
                    ],
                    [
                        'namespace' => '', 'short_class' => '', 'class' => '', 'type' => '', 'function' => 'test',
                        'file' => __FILE__, 'line' => 123,
                        'args' => [
                            [
                                'incomplete-object', 'BogusTestClass',
                            ],
                        ],
                    ],
                ],
            ],
        ], $flattened->toArray());
    }
}
