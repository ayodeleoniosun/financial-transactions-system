<?php

namespace Financial\Transactions\Tests\Transaction;

use Exception;
use Financial\Transactions\Accounts\Account;
use Financial\Transactions\Accounts\AccountManager;
use Financial\Transactions\Enums\FilterTransactionEnum;
use Financial\Transactions\Transactions\FilterTransactions;
use Financial\Transactions\Transactions\Operations\Deposit;
use Financial\Transactions\Transactions\Operations\Transfer;
use Financial\Transactions\Transactions\Operations\Withdraw;
use PHPUnit\Framework\TestCase;

final class FilterTransactionTest extends TestCase
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
    public function test_can_filter_transactions_by_due_date_in_ascending_order()
    {
        $transactions = $this->simulateAccountTransactions();

        $filterTransactions = new FilterTransactions($transactions);
        $filterTransactionsByDueDateAsc = $filterTransactions->filterTransactionsByDueDate();

        usort($filterTransactionsByDueDateAsc, function ($a, $b) {
            $this->assertGreaterThanOrEqual($a->dueDate, $b->dueDate);
        });
    }

    /**
     * @throws Exception
     */
    public function simulateAccountTransactions(): array
    {
        //create 3 deposits
        $deposit = new Deposit($this->getAccount->getAccountNumber(), 3000, 'This is the first deposit', date("Y-m-d H:i:s"));
        $deposit->handle($this->getAccount);

        $deposit = new Deposit($this->getAccount->getAccountNumber(), 4000, 'This is the second deposit', date("Y-m-d H:i:s"));
        $deposit->handle($this->getAccount);

        $deposit = new Deposit($this->getAccount->getAccountNumber(), 4000, 'This is the third deposit', date("Y-m-d H:i:s"));
        $deposit->handle($this->getAccount);

        // create 2 withdrawals
        $withdraw = new Withdraw($this->getAccount->getAccountNumber(), 1000, 'This is the first withdrawal', date("Y-m-d H:i:s"));
        $withdraw->handle($this->getAccount);

        $withdraw = new Withdraw($this->getAccount->getAccountNumber(), 1500, 'This is the second withdrawal', date("Y-m-d H:i:s"));
        $withdraw->handle($this->getAccount);

        // create 2 transfers
        $transfer = new Transfer($this->getAccount->getAccountNumber(), 1000, 'This is the first transfer', date("Y-m-d H:i:s"), $this->recipient->number);
        $transfer->handle($this->getAccount);
        
        $transfer = new Transfer($this->getAccount->getAccountNumber(), 2000, 'This is the second transfer', date("Y-m-d H:i:s"), $this->recipient->number);
        $transfer->handle($this->getAccount);

        return $this->getAccount->getAccountTransactions();
    }

    /**
     * @throws Exception
     */
    public function test_can_filter_transactions_by_comment_in_ascending_order()
    {
        $transactions = $this->simulateAccountTransactions();

        $filterTransactions = new FilterTransactions($transactions);
        $filterTransactionsByCommentAsc = $filterTransactions->filterTransactionsByComment();

        usort($filterTransactionsByCommentAsc, function ($a, $b) {
            $this->assertGreaterThanOrEqual($a->comment, $b->comment);
        });
    }

    /**
     * @throws Exception
     */
    public function test_can_filter_transactions_by_due_date_in_descending_order()
    {
        $transactions = $this->simulateAccountTransactions();

        $filterTransactions = new FilterTransactions($transactions);
        $filterTransactionsByDueDateAsc = $filterTransactions->filterTransactionsByDueDate(FilterTransactionEnum::DESCENDING);

        usort($filterTransactionsByDueDateAsc, function ($a, $b) {
            $this->assertLessThanOrEqual($a->dueDate, $b->dueDate);
        });
    }

    /**
     * @throws Exception
     */
    public function test_can_filter_transactions_by_comment_in_descending_order()
    {
        $transactions = $this->simulateAccountTransactions();

        $filterTransactions = new FilterTransactions($transactions);
        $filterTransactionsByCommentDesc = $filterTransactions->filterTransactionsByComment(FilterTransactionEnum::DESCENDING);

        usort($filterTransactionsByCommentDesc, function ($a, $b) {
            $this->assertLessThanOrEqual($a->comment, $b->comment);
        });
    }
}