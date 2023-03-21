<?php

namespace Financial\Transactions\Tests;

use Exception;
use Financial\Transactions\Account;
use Financial\Transactions\Enums\TransactionEnum;
use Financial\Transactions\TransactionManager;
use PHPUnit\Framework\TestCase;

class TransactionManagerTest extends TestCase
{
    protected TransactionManager $transactionManager;
    protected Account $account;

    public function setUp(): void
    {
        $this->transactionManager = new TransactionManager();
        $this->account = Account::getInstance();
    }

    /**
     * @throws Exception
     */
    public function test_cannot_deposit_into_an_invalid_account()
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage("Invalid account number.");
        $this->transactionManager->deposit(1000, 11111);
    }

    /**
     * @throws Exception
     */
    public function test_cannot_deposit_into_a_non_existing_account()
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage("Account does not exist.");
        $this->transactionManager->deposit(1000, 12345678911);
    }

    /**
     * @throws Exception
     */
    public function test_cannot_deposit_an_invalid_amount()
    {
        $account = $this->account->createAccount('Nameless User');

        $this->expectException(Exception::class);
        $this->expectExceptionMessage("Invalid amount. Try again");
        $this->transactionManager->deposit(0, $account->getAccountNumber());
    }

    /**
     * @throws Exception
     */
    public function test_can_deposit()
    {
        $account = $this->account->createAccount('Nameless User');

        $deposit = $this->transactionManager->deposit(1000, $account->getAccountNumber());

        $this->assertEquals(1000, $deposit->amount);
        $this->assertEquals(TransactionEnum::DEPOSIT, $deposit->type);
        $this->assertNull($deposit->sender);
        $this->assertEquals($account->getAccountNumber(), $deposit->recipient);
        $this->assertEquals(0, $deposit->old_balance);
        $this->assertEquals(1000, $deposit->new_balance);
    }
}