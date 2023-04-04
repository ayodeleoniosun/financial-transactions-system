<?php

namespace Financial\Transactions;

use Financial\Transactions\Enums\FilterTransactionEnum;
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
    $account->getAccount($account2->getAccountNumber());
} catch (\Exception $e) {
    echo $e->getMessage();
}

echo "\n";

$allAccounts = $account->getAllAccounts(); //get all accounts

$transactionManager = new TransactionManager();

//deposit
try {
    $transactionManager->execute(DepositService::class, 10000, $account1);
    $transactionManager->execute(DepositService::class, 5000, $account1);
    $transactionManager->execute(DepositService::class, 2000, $account2);
    $transactionManager->execute(DepositService::class, 4000, $account2);
    $transactionManager->execute(DepositService::class, 5000, $account3);
} catch (\Exception $e) {
    echo $e->getMessage();
}
/*
    account 1 balance after deposit - 15000
    account 2 balance after deposit - 6000
    account 3 balance after deposit - 5000
*/


//withdraw
try {
    $transactionManager->execute(WithdrawalService::class, 6000, $account1);
    $transactionManager->execute(WithdrawalService::class, 5000, $account1);
    $transactionManager->execute(WithdrawalService::class, 1500, $account2);
    $transactionManager->execute(WithdrawalService::class, 4000, $account3);
} catch (\Exception $e) {
    echo $e->getMessage();
}

/*
    account 1 balance after withdrawal - 4000
    account 2 balance after withdrawal - 4500
    account 3 balance after withdrawal - 1000
*/

sleep(1);

//transfer

try {
    $transactionManager->execute(TransferService::class, 1000, $account1, $account2);
    $transactionManager->execute(TransferService::class, 1000, $account1, $account3);
    $transactionManager->execute(TransferService::class, 1500, $account2, $account1);
    $transactionManager->execute(TransferService::class, 1000, $account2, $account3);
} catch (\Exception $e) {
    echo $e->getMessage();
}

///*
//    account 1 balance after transfer - 3500
//    account 2 balance after transfer - 3000
//    account 3 balance after transfer - 3000
//*/


$transactions = $transactionManager::getAccountTransactions($account1);

$ledger = new Ledger();

$deposits = $ledger->getAccountDepositTransactions($transactions);
$withdrawals = $ledger->getAccountWithdrawalTransactions($transactions);
$transfers = $ledger->getAccountTransferTransactions($transactions);

$filterTransactionsByDueDate = $ledger->filterTransactionsByDueDate($transactions);
$filterTransactionsByDueDateDesc = $ledger->filterTransactionsByDueDate($transactions, FilterTransactionEnum::DESCENDING);

$filterTransactionsByComment = $ledger->filterTransactionsByComment($transactions);
$filterTransactionsByCommentDesc = $ledger->filterTransactionsByComment($transactions, FilterTransactionEnum::DESCENDING);
