<?php

namespace Financial\Transactions\services;

use Exception;
use Financial\Transactions\Account;
use Financial\Transactions\contracts\BaseTransactionService;
use Financial\Transactions\contracts\TransactionServiceInterface;
use Financial\Transactions\Enums\TransactionEnum;

class WithdrawalService extends BaseTransactionService implements TransactionServiceInterface
{
    /**
     * @throws Exception
     */
    public function handler(float $amount, Account $account, Account|null $recipient = null): object
    {
        $oldBalance = $this->checkFundSufficiencyAndReturnBalance($amount, $account);

        $newBalance = $oldBalance - $amount;

        $payload = (object)[
            'id' => $this->generateTransactionId(),
            'comment' => "This is a withdrawal from {$account->getAccountNumber()}'s account",
            'amount' => $amount,
            'dueDate' => date("Y-m-d H:i:s"),
            'type' => $this->transactionType(),
            'sender' => null,
            'recipient' => $account->getAccountNumber(), //this is account withdrawn from
            'old_balance' => $oldBalance,
            'new_balance' => $newBalance,
        ];

        $account->setAccountBalance($newBalance);

        return $this->createTransaction($payload);
    }

    public function transactionType(): string
    {
        return TransactionEnum::WITHDRAW;
    }
}