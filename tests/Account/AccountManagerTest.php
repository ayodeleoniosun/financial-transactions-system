<?php

namespace Financial\Transactions\Tests\Account;

use Exception;
use Financial\Transactions\Accounts\AccountManager;
use PHPUnit\Framework\TestCase;

final class AccountManagerTest extends TestCase
{
    protected AccountManager $accountManager;

    public function setUp(): void
    {
        $this->accountManager = AccountManager::getInstance();
    }

    public function test_can_create_new_account()
    {
        $account = $this->accountManager->createAccount('John Doe');
        $this->assertEquals('John Doe', $account->name);
    }

    /**
     * @throws Exception
     */
    public function test_cannot_get_invalid_account()
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage("Account does not exist");
        $this->accountManager->getAccount(12345);
    }

    /**
     * @throws Exception
     */
    public function test_can_get_single_account()
    {
        $account = $this->accountManager->createAccount('Nameless User');
        $getAccount = $this->accountManager->getAccount($account->number);

        $this->assertEquals($getAccount->id, $account->id);
        $this->assertEquals($getAccount->name, $account->name);
        $this->assertEquals($getAccount->number, $account->number);
    }

    public function test_can_get_all_accounts()
    {
        $this->accountManager->createAccount('Ayodele Oniosun');
        $this->accountManager->createAccount('John Doe');
        $this->accountManager->createAccount('Nameless User');

        $accounts = $this->accountManager->getAllAccounts();

        $this->assertGreaterThan(0, $accounts);
    }
}
