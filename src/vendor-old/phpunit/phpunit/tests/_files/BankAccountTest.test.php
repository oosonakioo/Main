<?php

/*
 * This file is part of PHPUnit.
 *
 * (c) Sebastian Bergmann <sebastian@phpunit.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * Tests for the BankAccount class.
 *
 * @since      Class available since Release 2.3.0
 */
class BankAccountWithCustomExtensionTest extends PHPUnit_Framework_TestCase
{
    protected $ba;

    protected function setUp()
    {
        $this->ba = new BankAccount;
    }

    /**
     * @covers BankAccount::getBalance
     *
     * @group balanceIsInitiallyZero
     * @group specification
     */
    public function test_balance_is_initially_zero()
    {
        $this->assertEquals(0, $this->ba->getBalance());
    }

    /**
     * @covers BankAccount::withdrawMoney
     *
     * @group balanceCannotBecomeNegative
     * @group specification
     */
    public function test_balance_cannot_become_negative()
    {
        try {
            $this->ba->withdrawMoney(1);
        } catch (BankAccountException $e) {
            $this->assertEquals(0, $this->ba->getBalance());

            return;
        }

        $this->fail();
    }

    /**
     * @covers BankAccount::depositMoney
     *
     * @group balanceCannotBecomeNegative
     * @group specification
     */
    public function test_balance_cannot_become_negative2()
    {
        try {
            $this->ba->depositMoney(-1);
        } catch (BankAccountException $e) {
            $this->assertEquals(0, $this->ba->getBalance());

            return;
        }

        $this->fail();
    }

    /*
     * @covers BankAccount::getBalance
     * @covers BankAccount::depositMoney
     * @covers BankAccount::withdrawMoney
     * @group balanceCannotBecomeNegative
     */
    /*
    public function testDepositingAndWithdrawingMoneyWorks()
    {
        $this->assertEquals(0, $this->ba->getBalance());
        $this->ba->depositMoney(1);
        $this->assertEquals(1, $this->ba->getBalance());
        $this->ba->withdrawMoney(1);
        $this->assertEquals(0, $this->ba->getBalance());
    }
    */
}
