<?php

namespace Faker\Test\Provider;

use Faker\Provider\UserAgent;

class UserAgentTest extends \PHPUnit_Framework_TestCase
{
    public function test_random_user_agent()
    {
        $this->assertNotNull(UserAgent::userAgent());
    }

    public function test_firefox_user_agent()
    {
        $this->stringContains(' Firefox/', UserAgent::firefox());
    }

    public function test_safari_user_agent()
    {
        $this->stringContains('Safari/', UserAgent::safari());
    }

    public function test_internet_explorer_user_agent()
    {
        $this->assertStringStartsWith('Mozilla/5.0 (compatible; MSIE ', UserAgent::internetExplorer());
    }

    public function test_opera_user_agent()
    {
        $this->assertStringStartsWith('Opera/', UserAgent::opera());
    }

    public function test_chrome_user_agent()
    {
        $this->stringContains('(KHTML, like Gecko) Chrome/', UserAgent::chrome());
    }
}
