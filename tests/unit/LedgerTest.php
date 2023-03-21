<?php

namespace Financial\Transactions\Tests;

use Exception;
use Financial\Transactions\Account;
use Financial\Transactions\Enums\FilterTransactionEnum;
use Financial\Transactions\Enums\TransactionEnum;
use Financial\Transactions\Ledger;
use Financial\Transactions\TransactionManager;
use PHPUnit\Framework\TestCase;

final class LedgerTest extends TestCase
{
    protected TransactionManager $transactionManager;
    protected Ledger $ledger;
    protected Account $accountManager;
    protected object $account;
    protected object $recipient;

    public function setUp(): void
    {
        $this->transactionManager = new TransactionManager();
        $this->ledger = new Ledger();
        $this->accountManager = Account::getInstance();
        $this->account = $this->accountManager->createAccount('Nameless User');
        $this->recipient = $this->accountManager->createAccount('Ayodele Oniosun');
    }

    /**
     * @throws Exception
     */
    public function test_can_get_account_transactions()
    {
        $this->transactionManager->deposit(1000, $this->account->getAccountNumber());
        $this->transactionManager->deposit(3000, $this->account->getAccountNumber());
        $this->transactionManager->deposit(4000, $this->account->getAccountNumber());

        $transactions = $this->transactionManager->getAccountTransactions($this->account);

        $this->assertGreaterThan(0, $transactions);
    }

    /**
     * @throws Exception
     */
    public function test_can_get_account_deposit_transactions()
    {
        $this->simulateAccountTransactions();

        $transactions = $this->transactionManager->getAccountTransactions($this->account);

        $deposits = $this->ledger->getAccountDepositTransactions($transactions);

        array_walk($deposits, function ($transaction) {
            $this->assertEquals(TransactionEnum::DEPOSIT, $transaction->type);
            $this->assertEquals($this->account->getAccountNumber(), $transaction->recipient);
            $this->assertNotNull($transaction->old_balance);
            $this->assertNotNull($transaction->new_balance);
            $this->assertNull($transaction->sender);
        });

        $this->assertGreaterThan(0, $deposits);
    }

    /**
     * @throws Exception
     */
    public function simulateAccountTransactions()
    {
        // create deposits
        $this->transactionManager->deposit(4000, $this->account->getAccountNumber());
        $this->transactionManager->deposit(5000, $this->account->getAccountNumber());
        $this->transactionManager->deposit(6000, $this->account->getAccountNumber());
        sleep(1); // This is to ensure that all transactions does not have the same due date

        // create withdrawals
        $this->transactionManager->withdraw(2000, $this->account->getAccountNumber());
        $this->transactionManager->withdraw(1000, $this->account->getAccountNumber());
        sleep(1);

        // create transfers
        $this->transactionManager->transfer(2000, $this->account->getAccountNumber(), $this->recipient->getAccountNumber());
        $this->transactionManager->transfer(2000, $this->account->getAccountNumber(), $this->recipient->getAccountNumber());
        $this->transactionManager->transfer(500, $this->recipient->getAccountNumber(), $this->account->getAccountNumber());
    }

    /**
     * @throws Exception
     */
    public function test_can_get_account_withdrawal_transactions()
    {
        $this->simulateAccountTransactions();

        $transactions = $this->transactionManager->getAccountTransactions($this->account);

        $withdrawals = $this->ledger->getAccountWithdrawalTransactions($transactions);

        array_walk($withdrawals, function ($transaction) {
            $this->assertEquals(TransactionEnum::WITHDRAW, $transaction->type);
            $this->assertEquals($this->account->getAccountNumber(), $transaction->recipient);
            $this->assertNotNull($transaction->old_balance);
            $this->assertNotNull($transaction->new_balance);
            $this->assertNull($transaction->sender);
        });

        $this->assertGreaterThan(0, $withdrawals);
    }

    /**
     * @throws Exception
     */
    public function test_can_get_account_transfer_transactions()
    {
        $this->simulateAccountTransactions();

        $transactions = $this->transactionManager->getAccountTransactions($this->account);

        $transfers = $this->ledger->getAccountTransferTransactions($transactions);

        array_walk($transfers, function ($transaction) {
            $this->assertEquals(TransactionEnum::TRANSFER, $transaction->type);
            $this->assertNotNull($transaction->sender_old_balance);
            $this->assertNotNull($transaction->sender_new_balance);
            $this->assertNotNull($transaction->recipient_old_balance);
            $this->assertNotNull($transaction->recipient_new_balance);
            $this->assertNotEquals($transaction->sender, $transaction->recipient);
        });

        $this->assertGreaterThan(0, $transfers);
    }

    /**
     * @throws Exception
     */
    public function test_can_filter_transactions_by_due_date_in_ascending_order()
    {
        $this->simulateAccountTransactions();

        $transactions = $this->transactionManager->getAccountTransactions($this->account);

        $filterTransactionsByDueDateAsc = $this->ledger->filterTransactionsByDueDate($transactions);

        usort($filterTransactionsByDueDateAsc, function ($a, $b) {
            $this->assertGreaterThanOrEqual($a->dueDate, $b->dueDate);
        });
    }

    /**
     * @throws Exception
     */
    public function test_can_filter_transactions_by_due_date_in_descending_order()
    {
        $this->simulateAccountTransactions();

        $transactions = $this->transactionManager->getAccountTransactions($this->account);

        $filterTransactionsByDueDateDesc = $this->ledger->filterTransactionsByDueDate($transactions, FilterTransactionEnum::DESCENDING);

        usort($filterTransactionsByDueDateDesc, function ($a, $b) {
            $this->assertLessThanOrEqual($a->dueDate, $b->dueDate);
        });
    }

    /**
     * @throws Exception
     */
    public function test_can_filter_transactions_by_comment_in_ascending_order()
    {
        $this->simulateAccountTransactions();

        $transactions = $this->transactionManager->getAccountTransactions($this->account);

        $filterTransactionsByCommentAsc = $this->ledger->filterTransactionsByComment($transactions);

        usort($filterTransactionsByCommentAsc, function ($a, $b) {
            $this->assertGreaterThanOrEqual($a->comment, $b->comment);
        });
    }

    /**
     * @throws Exception
     */
    public function test_can_filter_transactions_by_comment_in_descending_order()
    {
        $this->simulateAccountTransactions();

        $transactions = $this->transactionManager->getAccountTransactions($this->account);

        $filterTransactionsByCommentDesc = $this->ledger->filterTransactionsByComment($transactions, FilterTransactionEnum::DESCENDING);

        usort($filterTransactionsByCommentDesc, function ($a, $b) {
            $this->assertLessThanOrEqual($a->comment, $b->comment);
        });
    }
}
