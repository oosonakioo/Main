<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\Routing\Tests\Annotation;

use Symfony\Component\Routing\Annotation\Route;

class RouteTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @expectedException \BadMethodCallException
     */
    public function test_invalid_route_parameter()
    {
        $route = new Route(['foo' => 'bar']);
    }

    /**
     * @dataProvider getValidParameters
     */
    public function test_route_parameters($parameter, $value, $getter)
    {
        $route = new Route([$parameter => $value]);
        $this->assertEquals($route->$getter(), $value);
    }

    public function getValidParameters()
    {
        return [
            ['value', '/Blog', 'getPath'],
            ['requirements', ['locale' => 'en'], 'getRequirements'],
            ['options', ['compiler_class' => 'RouteCompiler'], 'getOptions'],
            ['name', 'blog_index', 'getName'],
            ['defaults', ['_controller' => 'MyBlogBundle:Blog:index'], 'getDefaults'],
            ['schemes', ['https'], 'getSchemes'],
            ['methods', ['GET', 'POST'], 'getMethods'],
            ['host', '{locale}.example.com', 'getHost'],
            ['condition', 'context.getMethod() == "GET"', 'getCondition'],
        ];
    }
}
