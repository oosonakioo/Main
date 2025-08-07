<?php

class Swift_Transport_Esmtp_Auth_CramMd5AuthenticatorTest extends \SwiftMailerTestCase
{
    private $_agent;

    protected function setUp()
    {
        $this->_agent = $this->getMockery('Swift_Transport_SmtpAgent')->shouldIgnoreMissing();
    }

    public function test_keyword_is_cram_md5()
    {
        /* -- RFC 2195, 2.
        The authentication type associated with CRAM is "CRAM-MD5".
        */

        $cram = $this->_getAuthenticator();
        $this->assertEquals('CRAM-MD5', $cram->getAuthKeyword());
    }

    public function test_successful_authentication()
    {
        $cram = $this->_getAuthenticator();

        $this->_agent->shouldReceive('executeCommand')
            ->once()
            ->with("AUTH CRAM-MD5\r\n", [334])
            ->andReturn('334 '.base64_encode('<foo@bar>')."\r\n");
        $this->_agent->shouldReceive('executeCommand')
            ->once()
            ->with(\Mockery::any(), [235]);

        $this->assertTrue($cram->authenticate($this->_agent, 'jack', 'pass'),
            '%s: The buffer accepted all commands authentication should succeed'
        );
    }

    public function test_authentication_failure_send_rset_and_return_false()
    {
        $cram = $this->_getAuthenticator();

        $this->_agent->shouldReceive('executeCommand')
            ->once()
            ->with("AUTH CRAM-MD5\r\n", [334])
            ->andReturn('334 '.base64_encode('<foo@bar>')."\r\n");
        $this->_agent->shouldReceive('executeCommand')
            ->once()
            ->with(\Mockery::any(), [235])
            ->andThrow(new Swift_TransportException(''));
        $this->_agent->shouldReceive('executeCommand')
            ->once()
            ->with("RSET\r\n", [250]);

        $this->assertFalse($cram->authenticate($this->_agent, 'jack', 'pass'),
            '%s: Authentication fails, so RSET should be sent'
        );
    }

    private function _getAuthenticator()
    {
        return new Swift_Transport_Esmtp_Auth_CramMd5Authenticator;
    }
}
