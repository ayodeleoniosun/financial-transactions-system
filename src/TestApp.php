<?php

namespace Financial\Transactions;

require_once "vendor/autoload.php";

// create new account
$account = Account::getInstance();

$account1 = $account->createAccount('John Doe');
$account2 = $account->createAccount('Nameless User');
$account3 = $account->createAccount('Ayodele Oniosun');

// get single account
try {
    $getAccount = $account->getAccount($account2->getAccountNumber());
    //var_dump($getAccount);
} catch (\Exception $e) {
    echo $e->getMessage();
}

echo "\n";

//get all accounts
$allAccounts = $account->getAllAccounts();

//var_dump($allAccounts);

$transactionManager = new TransactionManager();

try {
    $transactionManager->deposit(10000, $account1->getAccountNumber());
    $transactionManager->deposit(2000, $account1->getAccountNumber());
    $transactionManager->deposit(3000, $account2->getAccountNumber());
    $transactionManager->deposit(4000, $account2->getAccountNumber());
    $transactionManager->deposit(5000, $account3->getAccountNumber());
} catch (\Exception $e) {
    echo $e->getMessage();
}


try {
    $transactionManager->withdraw(1000, $account1->getAccountNumber());
    $transactionManager->withdraw(500, $account1->getAccountNumber());
    $transactionManager->withdraw(2500, $account2->getAccountNumber());
    $transactionManager->withdraw(3000, $account3->getAccountNumber());
} catch (\Exception $e) {
    echo $e->getMessage();
}

/*
    account 1 balance - 10500
    account 2 balance - 4500
    account 3 balance - 2000
*/

try {
    $transactionManager->transfer(1000, $account1->getAccountNumber(), $account2->getAccountNumber());
    // sender old - 10500, sender new - 9500
    // recipient old - 4500, recipient new  - 5500

    $transactionManager->transfer(1000, $account1->getAccountNumber(), $account3->getAccountNumber());
    // sender old - 9500, sender new - 8500
    // recipient old - 2000, recipient new  - 3000

    $transactionManager->transfer(500, $account2->getAccountNumber(), $account1->getAccountNumber());
    // sender old - 5500, sender new - 5000
    // recipient old - 8500, recipient new  - 9000

    $transactionManager->transfer(2500, $account2->getAccountNumber(), $account3->getAccountNumber());
    // sender old - 5000, sender new - 2500
    // recipient old - 3000, recipient new  - 5500

    $transactionManager->transfer(3000, $account3->getAccountNumber(), $account1->getAccountNumber());
    // sender old - 5500, sender new - 2500
    // recipient old - 9000, recipient new  - 12000

} catch (\Exception $e) {
    echo $e->getMessage();
}

var_dump($transactionManager->getTransactions());