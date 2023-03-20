<?php

namespace Financial\Transactions\Tests\Transaction;

use Exception;
use Financial\Transactions\Accounts\Account;
use Financial\Transactions\Accounts\AccountManager;
use Financial\Transactions\Enums\TransactionEnum;
use Financial\Transactions\Transactions\Operations\Deposit;
use Financial\Transactions\Transactions\Operations\Withdraw;
use PHPUnit\Framework\TestCase;

final class WithdrawTest extends TestCase
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
    public function test_cannot_withdraw_amount_with_empty_fields()
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage("Please fill all fields");

        $withdraw = new Withdraw($this->getAccount->getAccountNumber(), 0, '', date("Y-m-d H:i:s"));
        $withdraw->handle($this->getAccount);
    }

    /**
     * @throws Exception
     */
    public function test_cannot_withdraw_negative_amount()
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage("The amount to be withdrawn must be greater than 0");

        $withdraw = new Withdraw($this->getAccount->getAccountNumber(), -50, 'This is the first withdrawal', date("Y-m-d H:i:s"));
        $withdraw->handle($this->getAccount);
    }

    /**
     * @throws Exception
     */
    public function test_cannot_withdraw_amount_greater_than_the_balance()
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage("Insufficient fund");

        $withdraw = new Withdraw($this->getAccount->getAccountNumber(), 500, 'This is the first withdrawal', date("Y-m-d H:i:s"));
        $withdraw->handle($this->getAccount);
    }

    /**
     * @throws Exception
     */
    public function test_can_withdraw()
    {
        $deposit = new Deposit($this->getAccount->getAccountNumber(), 3000, 'This is the first deposit', date("Y-m-d H:i:s"));
        $deposit->handle($this->getAccount);

        $withdraw = new Withdraw($this->getAccount->getAccountNumber(), 1000, 'This is the first withdrawal', date("Y-m-d H:i:s"));
        $withdraw->handle($this->getAccount);

        $this->assertEquals(TransactionEnum::WITHDRAW, $withdraw->getType());
        $this->assertEquals(1000, $withdraw->getAmount());
        $this->assertEquals('This is the first withdrawal', $withdraw->getComment());
        $this->assertEquals(date("Y-m-d H:i:s"), $withdraw->getDueDate());
        $this->assertEquals($this->getAccount->getAccountNumber(), $withdraw->getAccountNumber());
        $this->assertNull($withdraw->getRecipient());
    }
}
