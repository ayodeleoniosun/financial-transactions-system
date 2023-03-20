<?php

namespace Financial\Transactions\Tests\Transaction;

use Exception;
use Financial\Transactions\Accounts\Account;
use Financial\Transactions\Accounts\AccountManager;
use Financial\Transactions\Enums\TransactionEnum;
use Financial\Transactions\Transactions\Operations\Deposit;
use Financial\Transactions\Transactions\Operations\Transfer;
use PHPUnit\Framework\TestCase;

final class TransferTest extends TestCase
{
    protected AccountManager $accountManager;
    protected object $account;
    protected object $recipient;
    protected Account $getAccount;

    public function setUp(): void
    {
        $this->accountManager = AccountManager::getInstance();
        $this->account = $this->accountManager->createAccount('Nameless User');
        $this->recipient = $this->accountManager->createAccount('John Doe');
        $this->getAccount = new Account($this->account->number);
    }

    /**
     * @throws Exception
     */
    public function test_cannot_transfer_amount_with_empty_fields()
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage("Please fill all fields");

        $transfer = new Transfer($this->getAccount->getAccountNumber(), 0, '', date("Y-m-d H:i:s"), $this->recipient->number);
        $transfer->handle($this->getAccount);
    }

    /**
     * @throws Exception
     */
    public function test_cannot_transfer_negative_amount()
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage("The amount to be transferred must be greater than 0");

        $transfer = new Transfer($this->getAccount->getAccountNumber(), -50, 'This is the first transfer', date("Y-m-d H:i:s"), $this->recipient->number);
        $transfer->handle($this->getAccount);
    }

    /**
     * @throws Exception
     */
    public function test_cannot_transfer_funds_to_yourself()
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage("You cannot transfer funds to yourself");

        $transfer = new Transfer($this->getAccount->getAccountNumber(), 500, 'This is the first transfer', date("Y-m-d H:i:s"), $this->getAccount->getAccountNumber());
        $transfer->handle($this->getAccount);
    }

    /**
     * @throws Exception
     */
    public function test_cannot_transfer_amount_greater_than_the_balance()
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage("Insufficient fund");

        $transfer = new Transfer($this->getAccount->getAccountNumber(), 500, 'This is the first transfer', date("Y-m-d H:i:s"), $this->recipient->number);
        $transfer->handle($this->getAccount);
    }
    
    /**
     * @throws Exception
     */
    public function test_can_transfer()
    {
        $deposit = new Deposit($this->getAccount->getAccountNumber(), 3000, 'This is the first deposit', date("Y-m-d H:i:s"));
        $deposit->handle($this->getAccount);

        $transfer = new Transfer($this->getAccount->getAccountNumber(), 1000, 'This is the first transfer', date("Y-m-d H:i:s"), $this->recipient->number);
        $transfer->handle($this->getAccount);

        $this->assertEquals(TransactionEnum::TRANSFER, $transfer->getType());
        $this->assertEquals(1000, $transfer->getAmount());
        $this->assertEquals('This is the first transfer', $transfer->getComment());
        $this->assertEquals(date("Y-m-d H:i:s"), $transfer->getDueDate());
        $this->assertEquals($this->getAccount->getAccountNumber(), $transfer->getAccountNumber());
        $this->assertEquals($this->recipient->number, $transfer->getRecipient());
    }
}