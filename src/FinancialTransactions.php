<?php

namespace Financial\Transactions;

require "vendor/autoload.php";

use Financial\Transactions\Accounts\AccountManager;

$accountManager = AccountManager::getInstance();

$accountManager->createAccount('John Doe');
$accountManager->createAccount('Nameless User');
$account = $accountManager->createAccount('Ayodele Oniosun');

try {
    $getAccount = $accountManager->getAccount($account->number);
} catch (\Exception $e) {
    echo $e->getMessage();
}

echo "\n";

$allAccounts = $accountManager->getAllAccounts();

var_dump($allAccounts);
