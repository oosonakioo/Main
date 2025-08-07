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

use Symfony\Component\ExpressionLanguage\ExpressionLanguage;
use Symfony\Component\HttpFoundation\ExpressionRequestMatcher;
use Symfony\Component\HttpFoundation\Request;

class ExpressionRequestMatcherTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @expectedException \LogicException
     */
    public function test_when_no_expression_is_set()
    {
        $expressionRequestMatcher = new ExpressionRequestMatcher;
        $expressionRequestMatcher->matches(new Request);
    }

    /**
     * @dataProvider provideExpressions
     */
    public function test_matches_when_parent_matches_is_true($expression, $expected)
    {
        $request = Request::create('/foo');
        $expressionRequestMatcher = new ExpressionRequestMatcher;

        $expressionRequestMatcher->setExpression(new ExpressionLanguage, $expression);
        $this->assertSame($expected, $expressionRequestMatcher->matches($request));
    }

    /**
     * @dataProvider provideExpressions
     */
    public function test_matches_when_parent_matches_is_false($expression)
    {
        $request = Request::create('/foo');
        $request->attributes->set('foo', 'foo');
        $expressionRequestMatcher = new ExpressionRequestMatcher;
        $expressionRequestMatcher->matchAttribute('foo', 'bar');

        $expressionRequestMatcher->setExpression(new ExpressionLanguage, $expression);
        $this->assertFalse($expressionRequestMatcher->matches($request));
    }

    public function provideExpressions()
    {
        return [
            ['request.getMethod() == method', true],
            ['request.getPathInfo() == path', true],
            ['request.getHost() == host', true],
            ['request.getClientIp() == ip', true],
            ['request.attributes.all() == attributes', true],
            ['request.getMethod() == method && request.getPathInfo() == path && request.getHost() == host && request.getClientIp() == ip &&  request.attributes.all() == attributes', true],
            ['request.getMethod() != method', false],
            ['request.getMethod() != method && request.getPathInfo() == path && request.getHost() == host && request.getClientIp() == ip &&  request.attributes.all() == attributes', false],
        ];
    }
}
