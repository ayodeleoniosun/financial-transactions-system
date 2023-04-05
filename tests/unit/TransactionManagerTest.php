<?php

namespace Financial\Transactions\Tests;

use Exception;
use Financial\Transactions\Account;
use Financial\Transactions\Enums\TransactionEnum;
use Financial\Transactions\Ledger;
use Financial\Transactions\services\DepositService;
use Financial\Transactions\services\TransferService;
use Financial\Transactions\services\WithdrawalService;
use Financial\Transactions\TransactionManager;
use PHPUnit\Framework\TestCase;

final class TransactionManagerTest extends TestCase
{
    protected TransactionManager $transactionManager;
    protected Ledger $ledger;
    protected Account $accountManager;
    protected object $account;
    protected object $recipient;

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
    public function test_can_get_account_transactions()
    {
        $this->simulateAccountTransactions();

        $transactions = $this->transactionManager->getAccountTransactions($this->account);

        $this->assertGreaterThan(0, $transactions);
    }

    /**
     * @throws Exception
     */
    public function simulateAccountTransactions()
    {
        // create deposits
        $this->transactionManager->execute(DepositService::class, 4000, $this->account);
        $this->transactionManager->execute(DepositService::class, 5000, $this->account);
        $this->transactionManager->execute(DepositService::class, 6000, $this->account);
        sleep(1); // This is to ensure that all transactions does not have the same due date

        // create withdrawals
        $this->transactionManager->execute(WithdrawalService::class, 2000, $this->account);
        $this->transactionManager->execute(WithdrawalService::class, 1000, $this->account);
        sleep(1);

        // create transfers
        $this->transactionManager->execute(TransferService::class, 2000, $this->account, $this->recipient);
        $this->transactionManager->execute(TransferService::class, 2000, $this->account, $this->recipient);
    }

    /**
     * @throws Exception
     */
    public function test_can_create_transactions()
    {
        $depositAmount = 1000;

        $payload = (object)[
            'comment' => "This is a deposit into {$this->account->getAccountNumber()}'s account",
            'amount' => $depositAmount,
            'dueDate' => date("Y-m-d H:i:s"),
            'type' => TransactionEnum::DEPOSIT,
            'sender' => null,
            'recipient' => $this->account->getAccountNumber(),
            'old_balance' => $this->account->getAccountBalance(),
            'new_balance' => $this->account->getAccountBalance() + $depositAmount
        ];

        $transaction = $this->transactionManager::createTransaction($payload);

        $this->assertEquals(1000, $transaction->amount);
        $this->assertEquals(TransactionEnum::DEPOSIT, $transaction->type);
        $this->assertNull($transaction->sender);
        $this->assertEquals($this->account->getAccountNumber(), $transaction->recipient);
        $this->assertEquals(0, $transaction->old_balance);
        $this->assertEquals(1000, $transaction->new_balance);
    }

    /**
     * @throws Exception
     */
    public function test_can_generate_transaction_id()
    {
        $transactionId = $this->transactionManager::generateTransactionId();

        $this->assertIsInt($transactionId);
    }

    /**
     * @throws Exception
     */
    public function test_can_get_all_transactions()
    {
        $this->simulateAccountTransactions();
        $transactions = $this->transactionManager::getTransactions();

        $this->assertIsArray($transactions);
        $this->assertGreaterThan(0, count($transactions));
    }

    /**
     * @throws Exception
     */
    public function test_can_return_insufficient_fund_exception()
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage("Insufficient fund");

        $this->transactionManager::checkFundSufficiencyAndReturnBalance(1000, $this->account);
    }

    /**
     * @throws Exception
     */
    public function test_can_perform_transaction_with_sufficient_fund()
    {
        $this->account->setAccountBalance(10000);

        $balance = $this->transactionManager::checkFundSufficiencyAndReturnBalance(1000, $this->account);

        $this->assertEquals(10000, $balance);
    }

    /**
     * @throws Exception
     */
    public function test_can_return_invalid_amount_exception()
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage("Invalid amount. Try again");

        $this->transactionManager::validateAmount(-10);
        $this->transactionManager::validateAmount(0);
    }

    /**
     * @throws Exception
     */
    public function test_can_execute_deposit_transaction()
    {
        $deposit = $this->transactionManager->execute(DepositService::class, 1000, $this->account);

        $this->assertEquals(1000, $deposit->amount);
        $this->assertEquals(TransactionEnum::DEPOSIT, $deposit->type);
        $this->assertNull($deposit->sender);
        $this->assertEquals($this->account->getAccountNumber(), $deposit->recipient);
        $this->assertEquals(0, $deposit->old_balance);
        $this->assertEquals(1000, $deposit->new_balance);
    }

    /**
     * @throws Exception
     */
    public function test_can_execute_withdrawal_transaction()
    {
        $this->account->setAccountBalance(10000);
        $withdraw = $this->transactionManager->execute(WithdrawalService::class, 2000, $this->account);

        $this->assertEquals(2000, $withdraw->amount);
        $this->assertEquals(TransactionEnum::WITHDRAW, $withdraw->type);
        $this->assertNull($withdraw->sender);
        $this->assertEquals($this->account->getAccountNumber(), $withdraw->recipient);
        $this->assertEquals(10000, $withdraw->old_balance);
        $this->assertEquals(8000, $withdraw->new_balance);
    }

    /**
     * @throws Exception
     */
    public function test_can_execute_transfer_transaction()
    {
        $this->account->setAccountBalance(3000);
        $this->recipient->setAccountBalance(1000);

        $transfer = $this->transactionManager->execute(TransferService::class, 1000, $this->account, $this->recipient);

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
