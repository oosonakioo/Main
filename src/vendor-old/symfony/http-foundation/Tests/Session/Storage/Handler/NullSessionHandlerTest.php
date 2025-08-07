<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\HttpFoundation\Tests\Session\Storage\Handler;

use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Session\Storage\Handler\NullSessionHandler;
use Symfony\Component\HttpFoundation\Session\Storage\NativeSessionStorage;

/**
 * Test class for NullSessionHandler.
 *
 * @author Drak <drak@zikula.org>
 *
 * @runTestsInSeparateProcesses
 *
 * @preserveGlobalState disabled
 */
class NullSessionHandlerTest extends \PHPUnit_Framework_TestCase
{
    public function test_save_handlers()
    {
        $storage = $this->getStorage();
        $this->assertEquals('user', ini_get('session.save_handler'));
    }

    public function test_session()
    {
        session_id('nullsessionstorage');
        $storage = $this->getStorage();
        $session = new Session($storage);
        $this->assertNull($session->get('something'));
        $session->set('something', 'unique');
        $this->assertEquals('unique', $session->get('something'));
    }

    public function test_nothing_is_persisted()
    {
        session_id('nullsessionstorage');
        $storage = $this->getStorage();
        $session = new Session($storage);
        $session->start();
        $this->assertEquals('nullsessionstorage', $session->getId());
        $this->assertNull($session->get('something'));
    }

    public function getStorage()
    {
        return new NativeSessionStorage([], new NullSessionHandler);
    }
}
