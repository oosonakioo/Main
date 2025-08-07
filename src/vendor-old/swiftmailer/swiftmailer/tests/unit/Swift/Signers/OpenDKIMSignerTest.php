<?php

/**
 * @todo
 */
class Swift_Signers_OpenDKIMSignerTest extends \SwiftMailerTestCase
{
    protected function setUp()
    {
        if (! extension_loaded('opendkim')) {
            $this->markTestSkipped(
                'Need OpenDKIM extension run these tests.'
            );
        }
    }

    public function test_basic_signing_header_manipulation() {}

    // Default Signing
    public function test_signing_defaults() {}

    // SHA256 Signing
    public function test_signing256() {}

    // Relaxed/Relaxed Hash Signing
    public function test_signing_relaxed_relaxed256() {}

    // Relaxed/Simple Hash Signing
    public function test_signing_relaxed_simple256() {}

    // Simple/Relaxed Hash Signing
    public function test_signing_simple_relaxed256() {}
}
