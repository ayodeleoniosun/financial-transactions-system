<?php

namespace Financial\Transactions\Tests\Transaction;

use Exception;
use Financial\Transactions\Accounts\Account;
use Financial\Transactions\Accounts\AccountManager;
use Financial\Transactions\Enums\TransactionEnum;
use Financial\Transactions\Transactions\Operations\Deposit;
use PHPUnit\Framework\TestCase;

final class DepositTest extends TestCase
{
    protected AccountManager $accountManager;
    protected object $account;
    protected Account $getAccount;

    public function setUp(): void
    {
        $this->accountManager = AccountManager::getInstance();
        $this->account = $this->accountManager->createAccount('Nameless User');
        $this->getAccount = new Account($this->account->number);
    }

    /**
     * @throws Exception
     */
    public function test_cannot_deposit_amount_with_empty_fields()
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage("Please fill all fields");

        $deposit = new Deposit($this->getAccount->getAccountNumber(), 0, '', date("Y-m-d H:i:s"));
        $deposit->handle($this->getAccount);
    }

    /**
     * @throws Exception
     */
    public function test_cannot_deposit_negative_amount()
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage("The amount to be deposited must be greater than 0");

        $deposit = new Deposit($this->getAccount->getAccountNumber(), -50, 'This is the first deposit', date("Y-m-d H:i:s"));
        $deposit->handle($this->getAccount);
    }

    /**
     * @throws Exception
     */
    public function test_can_deposit()
    {
        $accountManager = AccountManager::getInstance();
        $account = $accountManager->createAccount('Nameless User');
        $getAccount = new Account($account->number);

        $deposit = new Deposit($getAccount->getAccountNumber(), 3000, 'This is the first deposit', date("Y-m-d H:i:s"));
        $deposit->handle($getAccount);

        $this->assertEquals(TransactionEnum::DEPOSIT, $deposit->getType());
        $this->assertEquals(3000, $deposit->getAmount());
        $this->assertEquals('This is the first deposit', $deposit->getComment());
        $this->assertEquals(date("Y-m-d H:i:s"), $deposit->getDueDate());
        $this->assertEquals($getAccount->getAccountNumber(), $deposit->getAccountNumber());
        $this->assertNull($deposit->getRecipient());
    }
}
