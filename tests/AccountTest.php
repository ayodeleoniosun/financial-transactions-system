<?php

namespace Financial\Transactions\Tests;

use Exception;
use Financial\Transactions\Account;
use PHPUnit\Framework\TestCase;

final class AccountTest extends TestCase
{
    protected Account $account;

    public function setUp(): void
    {
        $this->account = Account::getInstance();
    }

    public function test_can_create_new_account()
    {
        $account = $this->account->createAccount('John Doe');
        $this->assertEquals('John Doe', $account->getName());
        $this->assertEquals(0.0, $account->getAccountBalance());
    }

    /**
     * @throws Exception
     */
    public function test_account_length_must_be_eleven_numeric_characters()
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage("Invalid account number.");
        $this->account->getAccount(12345);
    }

    /**
     * @throws Exception
     */
    public function test_account_does_not_exist()
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage("Account does not exist.");
        $this->account->getAccount(12345678911);
    }

    /**
     * @throws Exception
     */
    public function test_can_get_single_account()
    {
        $account = $this->account->createAccount('Nameless User');
        $getAccount = $this->account->getAccount($account->getAccountNumber());

        $this->assertEquals($getAccount->getId(), $account->getId());
        $this->assertEquals($getAccount->getName(), $account->getName());
        $this->assertEquals($getAccount->getAccountNumber(), $account->getAccountNumber());
    }

    public function test_can_get_all_accounts()
    {
        $this->account->createAccount('Ayodele Oniosun');
        $this->account->createAccount('John Doe');
        $this->account->createAccount('Nameless User');

        $accounts = $this->account->getAllAccounts();

        $this->assertGreaterThan(0, $accounts);
    }
}
