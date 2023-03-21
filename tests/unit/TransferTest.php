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
    protected Account $account;

    public function setUp(): void
    {
        $this->transactionManager = new TransactionManager();
        $this->account = Account::getInstance();
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
        $sender = $this->account->createAccount('Nameless User');

        $this->expectException(Exception::class);
        $this->expectExceptionMessage("You cannot transfer funds to yourself.");
        $this->transactionManager->transfer(1000, $sender->getAccountNumber(), $sender->getAccountNumber());
    }

    /**
     * @throws Exception
     */
    public function test_cannot_transfer_an_invalid_amount()
    {
        $sender = $this->account->createAccount('Nameless User');
        $recipient = $this->account->createAccount('Ayodele Oniosun');

        $this->expectException(Exception::class);
        $this->expectExceptionMessage("Invalid amount. Try again");
        $this->transactionManager->transfer(0, $sender->getAccountNumber(), $recipient->getAccountNumber());
    }

    /**
     * @throws Exception
     */
    public function test_cannot_transfer_from_a_low_account_balance()
    {
        $sender = $this->account->createAccount('Nameless User');
        $recipient = $this->account->createAccount('Ayodele Oniosun');

        $this->expectException(Exception::class);
        $this->expectExceptionMessage("Insufficient fund");
        $this->transactionManager->transfer(1000, $sender->getAccountNumber(), $recipient->getAccountNumber());
    }

    /**
     * @throws Exception
     */
    public function test_can_transfer()
    {
        $sender = $this->account->createAccount('Nameless User');
        $recipient = $this->account->createAccount('Ayodele Oniosun');

        $this->transactionManager->deposit(3000, $sender->getAccountNumber());
        $this->transactionManager->deposit(1000, $recipient->getAccountNumber());

        $transfer = $this->transactionManager->transfer(1000, $sender->getAccountNumber(), $recipient->getAccountNumber());

        $this->assertEquals(1000, $transfer->amount);
        $this->assertEquals(TransactionEnum::TRANSFER, $transfer->type);
        $this->assertEquals($sender->getAccountNumber(), $transfer->sender);
        $this->assertEquals($recipient->getAccountNumber(), $transfer->recipient);
        $this->assertEquals(3000, $transfer->sender_old_balance);
        $this->assertEquals(2000, $transfer->sender_new_balance);
        $this->assertEquals(1000, $transfer->recipient_old_balance);
        $this->assertEquals(2000, $transfer->recipient_new_balance);
    }
}
