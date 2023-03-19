<?php

namespace Financial\Transactions;

require "vendor/autoload.php";

use Financial\Transactions\Accounts\Account;
use Financial\Transactions\Accounts\AccountManager;
use Financial\Transactions\Enums\FilterTransactionEnum;
use Financial\Transactions\Transactions\FilterTransactions;
use Financial\Transactions\Transactions\Operations\Deposit;
use Financial\Transactions\Transactions\Operations\Transfer;
use Financial\Transactions\Transactions\Operations\Withdraw;
use Financial\Transactions\Transactions\TransactionFactory;

// create new account
$accountManager = AccountManager::getInstance();

$accountManager->createAccount('John Doe');
$accountManager->createAccount('Nameless User');
$newAccount = $accountManager->createAccount('Ayodele Oniosun');

// get single account
try {
    $getAccount = $accountManager->getAccount($newAccount->number);
    //var_dump($getAccount);
} catch (\Exception $e) {
    echo $e->getMessage();
}

echo "\n";

//get all accounts
$allAccounts = $accountManager->getAllAccounts();

//var_dump($allAccounts);


// deposit to an account

$account = new Account($getAccount->number);

$deposit = TransactionFactory::create(Deposit::class, $getAccount->number, 2000, 'This is the first deposit', date("Y-m-d H:i:s"));
$deposit->handle($account);

$deposit = TransactionFactory::create(Deposit::class, $getAccount->number, 3000, 'This is the second deposit', date("Y-m-d H:i:s"));
$deposit->handle($account);

$deposit = TransactionFactory::create(Deposit::class, $allAccounts[0]->number, 4000, 'This is the third deposit', date("Y-m-d H:i:s"));
$deposit->handle($account);

$deposit = TransactionFactory::create(Deposit::class, $allAccounts[0]->number, 5000, 'This is the fourth deposit', date("Y-m-d H:i:s"));
$deposit->handle($account);

//withdraw from an account

sleep(2);

$withdraw = TransactionFactory::create(Withdraw::class, $getAccount->number, 1000, 'This is the first withdrawal', date("Y-m-d H:i:s"));

try {
    $withdraw->handle($account);
} catch (\Exception $e) {
    echo $e->getMessage();
}

$withdraw = TransactionFactory::create(Withdraw::class, $getAccount->number, 500, 'This is the second withdrawal', date("Y-m-d H:i:s"));

try {
    $withdraw->handle($account);
} catch (\Exception $e) {
    echo $e->getMessage();
}


//transfer to another account
sleep(2);

$transfer = TransactionFactory::create(Transfer::class, $getAccount->number, 2000, 'This is a new transfer to ' . $allAccounts[0]->number, date("Y-m-d H:i:s"), $allAccounts[0]->number);

try {
    $transfer->handle($account);
} catch (\Exception $e) {
    echo $e->getMessage();
}

$transfer = TransactionFactory::create(Transfer::class, $getAccount->number, 2000, 'This is a new transfer to ' . $allAccounts[1]->number, date("Y-m-d H:i:s"), $allAccounts[0]->number);

try {
    $transfer->handle($account);
} catch (\Exception $e) {
    echo $e->getMessage();
}


//filter transactions by due date

$accountTransactions = $account->getAccountTransactions();
$filterTransaction = new FilterTransactions($accountTransactions);

$filterTransactionsByDueDate = $filterTransaction->filterTransactionsByDueDate();
$filterTransactionsByDueDateDesc = $filterTransaction->filterTransactionsByDueDate(FilterTransactionEnum::DESCENDING);
//var_dump($filterTransactionsByDueDateDesc);


//filter transactions by comments

$filterTransactionsByComment = $filterTransaction->filterTransactionsByComment();
$filterTransactionsByCommentDesc = $filterTransaction->filterTransactionsByComment(FilterTransactionEnum::DESCENDING);
var_dump($filterTransactionsByCommentDesc);
