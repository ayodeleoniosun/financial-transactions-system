<?php

namespace Financial\Transactions;

require "vendor/autoload.php";

use Financial\Transactions\Accounts\AccountManager;
use Financial\Transactions\Transactions\Deposit;
use Financial\Transactions\Transactions\TransactionCalculator;
use Financial\Transactions\Transactions\Transfer;
use Financial\Transactions\Transactions\Withdraw;

// create new account
$accountManager = AccountManager::getInstance();

$accountManager->createAccount('John Doe');
$accountManager->createAccount('Nameless User');
$account = $accountManager->createAccount('Ayodele Oniosun');

// get single account
try {
    $getAccount = $accountManager->getAccount($account->number);
    //var_dump($getAccount);
} catch (\Exception $e) {
    echo $e->getMessage();
}

echo "\n";

//get all accounts
$allAccounts = $accountManager->getAllAccounts();

//var_dump($allAccounts);


// deposit to an account

$transactionCalculator = new TransactionCalculator();

$deposit = new Deposit($getAccount->number, 2000, 'This is a new deposit', date('Y-m-d'));
$deposit->handle($transactionCalculator);

$deposit = new Deposit($getAccount->number, 3000, 'This is a new deposit', date('Y-m-d'));
$deposit->handle($transactionCalculator);

$deposit = new Deposit($allAccounts[0]->number, 4000, 'This is a new deposit', date('Y-m-d'));
$deposit->handle($transactionCalculator);

//withdraw from an account

$withdraw = new Withdraw($getAccount->number, 1000, 'This is a new withdrawal', date('Y-m-d'));

try {
    $withdraw->handle($transactionCalculator);
} catch (\Exception $e) {
    echo $e->getMessage();
}

$withdraw = new Withdraw($getAccount->number, 2000, 'This is a new withdrawal', date('Y-m-d'));

try {
    $withdraw->handle($transactionCalculator);
} catch (\Exception $e) {
    echo $e->getMessage();
}

//transfer to another account

$transfer = new Transfer($getAccount->number, 1000, 'This is a new transfer to ' . $allAccounts[0]->number, date('Y-m-d'), $allAccounts[0]->number);

try {
    $transfer->handle($transactionCalculator);
} catch (\Exception $e) {
    echo $e->getMessage();
}

$transfer = new Transfer($getAccount->number, 100, 'This is a new transfer to ' . $allAccounts[0]->number, date('Y-m-d'), $allAccounts[0]->number);

try {
    $transfer->handle($transactionCalculator);
} catch (\Exception $e) {
    echo $e->getMessage();
}
