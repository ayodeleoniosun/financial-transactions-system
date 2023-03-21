<?php

namespace Financial\Transactions\Tests;

use Exception;
use Financial\Transactions\Account;
use Financial\Transactions\Enums\TransactionEnum;
use Financial\Transactions\TransactionManager;
use PHPUnit\Framework\TestCase;

final class WithdrawTest extends TestCase
{
    protected TransactionManager $transactionManager;
    protected Account $accountManager;
    protected Account $account;

    public function setUp(): void
    {
        $this->transactionManager = new TransactionManager();
        $this->accountManager = Account::getInstance();
        $this->account = $this->accountManager->createAccount('Nameless User');
    }

    /**
     * @throws Exception
     */
    public function test_cannot_withdraw_from_an_invalid_account()
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage("Invalid account number.");

        $this->transactionManager->withdraw(1000, 11111);
    }

    /**
     * @throws Exception
     */
    public function test_cannot_withdraw_from_a_non_existing_account()
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage("Account does not exist.");

        $this->transactionManager->withdraw(1000, 12345678911);
    }

    /**
     * @throws Exception
     */
    public function test_cannot_withdraw_an_invalid_amount()
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage("Invalid amount. Try again");

        $this->transactionManager->withdraw(0, $this->account->getAccountNumber());
    }

    /**
     * @throws Exception
     */
    public function test_cannot_withdraw_from_a_low_account_balance()
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage("Insufficient fund");

        $this->transactionManager->withdraw(1000, $this->account->getAccountNumber());
    }

    /**
     * @throws Exception
     */
    public function test_can_withdraw()
    {
        $this->transactionManager->deposit(3000, $this->account->getAccountNumber());

        $withdraw = $this->transactionManager->withdraw(2000, $this->account->getAccountNumber());

        $this->assertEquals(2000, $withdraw->amount);
        $this->assertEquals(TransactionEnum::WITHDRAW, $withdraw->type);
        $this->assertNull($withdraw->sender);
        $this->assertEquals($this->account->getAccountNumber(), $withdraw->recipient);
        $this->assertEquals(3000, $withdraw->old_balance);
        $this->assertEquals(1000, $withdraw->new_balance);
    }
}
