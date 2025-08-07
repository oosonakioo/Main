<?php

class Swift_Transport_Esmtp_AuthHandlerTest extends \SwiftMailerTestCase
{
    private $_agent;

    protected function setUp()
    {
        $this->_agent = $this->getMockery('Swift_Transport_SmtpAgent')->shouldIgnoreMissing();
    }

    public function test_keyword_is_auth()
    {
        $auth = $this->_createHandler([]);
        $this->assertEquals('AUTH', $auth->getHandledKeyword());
    }

    public function test_username_can_be_set_and_fetched()
    {
        $auth = $this->_createHandler([]);
        $auth->setUsername('jack');
        $this->assertEquals('jack', $auth->getUsername());
    }

    public function test_password_can_be_set_and_fetched()
    {
        $auth = $this->_createHandler([]);
        $auth->setPassword('pass');
        $this->assertEquals('pass', $auth->getPassword());
    }

    public function test_auth_mode_can_be_set_and_fetched()
    {
        $auth = $this->_createHandler([]);
        $auth->setAuthMode('PLAIN');
        $this->assertEquals('PLAIN', $auth->getAuthMode());
    }

    public function test_mixin_methods()
    {
        $auth = $this->_createHandler([]);
        $mixins = $auth->exposeMixinMethods();
        $this->assertTrue(in_array('getUsername', $mixins),
            '%s: getUsername() should be accessible via mixin'
        );
        $this->assertTrue(in_array('setUsername', $mixins),
            '%s: setUsername() should be accessible via mixin'
        );
        $this->assertTrue(in_array('getPassword', $mixins),
            '%s: getPassword() should be accessible via mixin'
        );
        $this->assertTrue(in_array('setPassword', $mixins),
            '%s: setPassword() should be accessible via mixin'
        );
        $this->assertTrue(in_array('setAuthMode', $mixins),
            '%s: setAuthMode() should be accessible via mixin'
        );
        $this->assertTrue(in_array('getAuthMode', $mixins),
            '%s: getAuthMode() should be accessible via mixin'
        );
    }

    public function test_authenticators_are_called_according_to_params_after_ehlo()
    {
        $a1 = $this->_createMockAuthenticator('PLAIN');
        $a2 = $this->_createMockAuthenticator('LOGIN');

        $a1->shouldReceive('authenticate')
            ->never()
            ->with($this->_agent, 'jack', 'pass');
        $a2->shouldReceive('authenticate')
            ->once()
            ->with($this->_agent, 'jack', 'pass')
            ->andReturn(true);

        $auth = $this->_createHandler([$a1, $a2]);
        $auth->setUsername('jack');
        $auth->setPassword('pass');

        $auth->setKeywordParams(['CRAM-MD5', 'LOGIN']);
        $auth->afterEhlo($this->_agent);
    }

    public function test_authenticators_are_not_used_if_no_username_set()
    {
        $a1 = $this->_createMockAuthenticator('PLAIN');
        $a2 = $this->_createMockAuthenticator('LOGIN');

        $a1->shouldReceive('authenticate')
            ->never()
            ->with($this->_agent, 'jack', 'pass');
        $a2->shouldReceive('authenticate')
            ->never()
            ->with($this->_agent, 'jack', 'pass')
            ->andReturn(true);

        $auth = $this->_createHandler([$a1, $a2]);

        $auth->setKeywordParams(['CRAM-MD5', 'LOGIN']);
        $auth->afterEhlo($this->_agent);
    }

    public function test_several_authenticators_are_tried_if_needed()
    {
        $a1 = $this->_createMockAuthenticator('PLAIN');
        $a2 = $this->_createMockAuthenticator('LOGIN');

        $a1->shouldReceive('authenticate')
            ->once()
            ->with($this->_agent, 'jack', 'pass')
            ->andReturn(false);
        $a2->shouldReceive('authenticate')
            ->once()
            ->with($this->_agent, 'jack', 'pass')
            ->andReturn(true);

        $auth = $this->_createHandler([$a1, $a2]);
        $auth->setUsername('jack');
        $auth->setPassword('pass');

        $auth->setKeywordParams(['PLAIN', 'LOGIN']);
        $auth->afterEhlo($this->_agent);
    }

    public function test_first_authenticator_to_pass_breaks_chain()
    {
        $a1 = $this->_createMockAuthenticator('PLAIN');
        $a2 = $this->_createMockAuthenticator('LOGIN');
        $a3 = $this->_createMockAuthenticator('CRAM-MD5');

        $a1->shouldReceive('authenticate')
            ->once()
            ->with($this->_agent, 'jack', 'pass')
            ->andReturn(false);
        $a2->shouldReceive('authenticate')
            ->once()
            ->with($this->_agent, 'jack', 'pass')
            ->andReturn(true);
        $a3->shouldReceive('authenticate')
            ->never()
            ->with($this->_agent, 'jack', 'pass');

        $auth = $this->_createHandler([$a1, $a2]);
        $auth->setUsername('jack');
        $auth->setPassword('pass');

        $auth->setKeywordParams(['PLAIN', 'LOGIN', 'CRAM-MD5']);
        $auth->afterEhlo($this->_agent);
    }

    private function _createHandler($authenticators)
    {
        return new Swift_Transport_Esmtp_AuthHandler($authenticators);
    }

    private function _createMockAuthenticator($type)
    {
        $authenticator = $this->getMockery('Swift_Transport_Esmtp_Authenticator')->shouldIgnoreMissing();
        $authenticator->shouldReceive('getAuthKeyword')
            ->zeroOrMoreTimes()
            ->andReturn($type);

        return $authenticator;
    }
}
