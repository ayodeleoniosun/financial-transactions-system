<?php

namespace Financial\Transactions\Tests;

use Exception;
use Financial\Transactions\Account;
use Financial\Transactions\Enums\TransactionEnum;
use Financial\Transactions\TransactionManager;
use PHPUnit\Framework\TestCase;

final class TransferTest extends TestCase
{
    protected TransactionManager $transactionManager;
    protected Account $accountManager;
    protected Account $account;
    protected Account $recipient;

    public function setUp(): void
    {
        $this->transactionManager = new TransactionManager();
        $this->accountManager = Account::getInstance();
        $this->account = $this->accountManager->createAccount('Nameless User');
        $this->recipient = $this->accountManager->createAccount('Ayodele Oniosun');
    }

    /**
     * @throws Exception
     */
    public function test_cannot_transfer_from_and_to_invalid_accounts()
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage("Invalid account number.");

        $this->transactionManager->transfer(1000, 11111, 11111);
    }

    /**
     * @throws Exception
     */
    public function test_cannot_transfer_from_and_to_non_existing_accounts()
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage("Account does not exist.");

        $this->transactionManager->transfer(1000, 12345678911, 12345678911);
    }

    /**
     * @throws Exception
     */
    public function test_cannot_transfer_to_yourself()
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage("You cannot transfer funds to yourself.");

        $this->transactionManager->transfer(1000, $this->account->getAccountNumber(), $this->account->getAccountNumber());
    }

    /**
     * @throws Exception
     */
    public function test_cannot_transfer_an_invalid_amount()
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage("Invalid amount. Try again");

        $this->transactionManager->transfer(0, $this->account->getAccountNumber(), $this->recipient->getAccountNumber());
    }

    /**
     * @throws Exception
     */
    public function test_cannot_transfer_from_a_low_account_balance()
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage("Insufficient fund");
        $this->transactionManager->transfer(1000, $this->account->getAccountNumber(), $this->recipient->getAccountNumber());
    }

    /**
     * @throws Exception
     */
    public function test_can_transfer()
    {
        $this->transactionManager->deposit(3000, $this->account->getAccountNumber());
        $this->transactionManager->deposit(1000, $this->recipient->getAccountNumber());

        $transfer = $this->transactionManager->transfer(1000, $this->account->getAccountNumber(), $this->recipient->getAccountNumber());

        $this->assertEquals(1000, $transfer->amount);
        $this->assertEquals(TransactionEnum::TRANSFER, $transfer->type);
        $this->assertEquals($this->account->getAccountNumber(), $transfer->sender);
        $this->assertEquals($this->recipient->getAccountNumber(), $transfer->recipient);
        $this->assertEquals(3000, $transfer->sender_old_balance);
        $this->assertEquals(2000, $transfer->sender_new_balance);
        $this->assertEquals(1000, $transfer->recipient_old_balance);
        $this->assertEquals(2000, $transfer->recipient_new_balance);
    }
}
