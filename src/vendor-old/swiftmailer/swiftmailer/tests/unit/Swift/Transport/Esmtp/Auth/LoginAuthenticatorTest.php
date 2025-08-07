<?php

class Swift_Transport_Esmtp_Auth_LoginAuthenticatorTest extends \SwiftMailerTestCase
{
    private $_agent;

    protected function setUp()
    {
        $this->_agent = $this->getMockery('Swift_Transport_SmtpAgent')->shouldIgnoreMissing();
    }

    public function test_keyword_is_login()
    {
        $login = $this->_getAuthenticator();
        $this->assertEquals('LOGIN', $login->getAuthKeyword());
    }

    public function test_successful_authentication()
    {
        $login = $this->_getAuthenticator();

        $this->_agent->shouldReceive('executeCommand')
            ->once()
            ->with("AUTH LOGIN\r\n", [334]);
        $this->_agent->shouldReceive('executeCommand')
            ->once()
            ->with(base64_encode('jack')."\r\n", [334]);
        $this->_agent->shouldReceive('executeCommand')
            ->once()
            ->with(base64_encode('pass')."\r\n", [235]);

        $this->assertTrue($login->authenticate($this->_agent, 'jack', 'pass'),
            '%s: The buffer accepted all commands authentication should succeed'
        );
    }

    public function test_authentication_failure_send_rset_and_return_false()
    {
        $login = $this->_getAuthenticator();

        $this->_agent->shouldReceive('executeCommand')
            ->once()
            ->with("AUTH LOGIN\r\n", [334]);
        $this->_agent->shouldReceive('executeCommand')
            ->once()
            ->with(base64_encode('jack')."\r\n", [334]);
        $this->_agent->shouldReceive('executeCommand')
            ->once()
            ->with(base64_encode('pass')."\r\n", [235])
            ->andThrow(new Swift_TransportException(''));
        $this->_agent->shouldReceive('executeCommand')
            ->once()
            ->with("RSET\r\n", [250]);

        $this->assertFalse($login->authenticate($this->_agent, 'jack', 'pass'),
            '%s: Authentication fails, so RSET should be sent'
        );
    }

    private function _getAuthenticator()
    {
        return new Swift_Transport_Esmtp_Auth_LoginAuthenticator;
    }
}
