<?php

use Symfony\Component\Routing\Exception\MethodNotAllowedException;
use Symfony\Component\Routing\Exception\ResourceNotFoundException;
use Symfony\Component\Routing\RequestContext;

/**
 * ProjectUrlMatcher.
 *
 * This class has been auto-generated
 * by the Symfony Routing Component.
 */
class ProjectUrlMatcher extends Symfony\Component\Routing\Matcher\UrlMatcher
{
    /**
     * Constructor.
     */
    public function __construct(RequestContext $context)
    {
        $this->context = $context;
    }

    public function match($pathinfo)
    {
        $allow = [];
        $pathinfo = rawurldecode($pathinfo);
        $context = $this->context;
        $request = $this->request;

        if (strpos($pathinfo, '/rootprefix') === 0) {
            // static
            if ($pathinfo === '/rootprefix/test') {
                return ['_route' => 'static'];
            }

            // dynamic
            if (preg_match('#^/rootprefix/(?P<var>[^/]++)$#s', $pathinfo, $matches)) {
                return $this->mergeDefaults(array_replace($matches, ['_route' => 'dynamic']), []);
            }

        }

        // with-condition
        if ($pathinfo === '/with-condition' && ($context->getMethod() == 'GET')) {
            return ['_route' => 'with-condition'];
        }

        throw count($allow) > 0 ? new MethodNotAllowedException(array_unique($allow)) : new ResourceNotFoundException;
    }
}
