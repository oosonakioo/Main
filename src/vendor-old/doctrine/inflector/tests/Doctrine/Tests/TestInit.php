<?php

/*
 * This file bootstraps the test environment.
 */

namespace Doctrine\Tests;

error_reporting(E_ALL | E_STRICT);

// register silently failing autoloader
spl_autoload_register(function ($class) {
    if (strpos($class, 'Doctrine\Tests\\') === 0) {
        $path = __DIR__.'/../../'.strtr($class, '\\', '/').'.php';
        if (is_file($path) && is_readable($path)) {
            require_once $path;

            return true;
        }
    } elseif (strpos($class, 'Doctrine\Common\\') === 0) {
        $path = __DIR__.'/../../../lib/'.($class = strtr($class, '\\', '/')).'.php';
        if (is_file($path) && is_readable($path)) {
            require_once $path;

            return true;
        }
    }
});
