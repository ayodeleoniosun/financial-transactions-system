<?php

namespace Financial\Transactions\Tests;

use Exception;
use Financial\Transactions\Account;
use Financial\Transactions\Enums\TransactionEnum;
use Financial\Transactions\services\TransferService;
use PHPUnit\Framework\TestCase;

final class TransferTest extends TestCase
{
    protected TransferService $transferService;
    protected Account $accountManager;
    protected Account $account;
    protected Account $recipient;

    public function setUp(): void
    {
        $this->transferService = new TransferService();
        $this->accountManager = Account::getInstance();
        $this->account = $this->accountManager->createAccount('Nameless User');
        $this->recipient = $this->accountManager->createAccount('Ayodele Oniosun');
    }

    /**
     * @throws Exception
     */
    public function test_cannot_transfer_to_yourself()
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage("You cannot transfer funds to yourself.");

        $this->transferService::handler(1000, $this->account, $this->account);
    }

    /**
     * @throws Exception
     */
    public function test_cannot_transfer_an_invalid_amount()
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage("Invalid amount. Try again");

        $this->transferService::handler(0, $this->account, $this->recipient);
    }

    /**
     * @throws Exception
     */
    public function test_cannot_transfer_from_a_low_account_balance()
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage("Insufficient fund");
        $this->transferService::handler(1000, $this->account, $this->recipient);
    }

    /**
     * @throws Exception
     */
    public function test_can_transfer()
    {
        $this->account->setAccountBalance(3000);
        $this->recipient->setAccountBalance(1000);

        $transfer = $this->transferService::handler(1000, $this->account, $this->recipient);

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
