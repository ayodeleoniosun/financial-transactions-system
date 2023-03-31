<?php

namespace Financial\Transactions;

use Financial\Transactions\services\DepositService;
use Financial\Transactions\services\TransferService;
use Financial\Transactions\services\WithdrawalService;

require_once "vendor/autoload.php";

// create new account
$account = Account::getInstance();

$account1 = $account->createAccount('John Doe');
$account2 = $account->createAccount('Nameless User');
$account3 = $account->createAccount('Ayodele Oniosun');

// get single account
try {
    $getAccount = $account->getAccount($account2->getAccountNumber());
} catch (\Exception $e) {
    echo $e->getMessage();
}

echo "\n";

//get all accounts
$allAccounts = $account->getAllAccounts();

$transactionManager = new TransactionManager();

//deposit

$deposit = new DepositService();

try {
    $deposit->handler(10000, $account1);
    $deposit->handler(5000, $account1);
    $deposit->handler(2000, $account2);
    $deposit->handler(4000, $account2);
    $deposit->handler(5000, $account3);
} catch (\Exception $e) {
    echo $e->getMessage();
}

/*
    account 1 balance after deposit - 15000
    account 2 balance after deposit - 6000
    account 3 balance after deposit - 5000
*/

sleep(1); // This is to ensure that all transactions does not have the same due date

//withdraw

$withdrawal = new WithdrawalService();

try {
    $withdrawal->handler(6000, $account1);
    $withdrawal->handler(5000, $account1);

    sleep(1); // This is to ensure that all transactions does not have the same due date

    $withdrawal->handler(1500, $account2);
    $withdrawal->handler(4000, $account3);
} catch (\Exception $e) {
    echo $e->getMessage();
}

/*
    account 1 balance after withdrawal - 4000
    account 2 balance after withdrawal - 4500
    account 3 balance after withdrawal - 1000
*/

sleep(1);

$transfer = new TransferService();

try {
    $transfer->handler(1000, $account1, $account2);
    $transfer->handler(1000, $account1, $account3);

    sleep(1);

    $transfer->handler(1500, $account2, $account1);
    $transfer->handler(1000, $account2, $account3);
} catch (\Exception $e) {
    echo $e->getMessage();
}

/*
    account 1 balance after transfer - 3500
    account 2 balance after transfer - 3000
    account 3 balance after transfer - 3000
*/

//
//$transactions = $transactionManager->getAccountTransactions($account1);
//
//$ledger = new Ledger();
//
//$deposits = $ledger->getAccountDepositTransactions($transactions);
//$withdrawals = $ledger->getAccountWithdrawalTransactions($transactions);
//$transfers = $ledger->getAccountTransferTransactions($transactions);
//
//
//$filterTransactionsByDueDate = $ledger->filterTransactionsByDueDate($transactions);
//$filterTransactionsByDueDateDesc = $ledger->filterTransactionsByDueDate($transactions, FilterTransactionEnum::DESCENDING);
//
//$filterTransactionsByComment = $ledger->filterTransactionsByComment($transactions);
//$filterTransactionsByCommentDesc = $ledger->filterTransactionsByComment($transactions, FilterTransactionEnum::DESCENDING);
