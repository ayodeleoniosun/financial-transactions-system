<?php

namespace Financial\Transactions\services;

use Exception;
use Financial\Transactions\Account;
use Financial\Transactions\contracts\BaseTransactionService;
use Financial\Transactions\contracts\TransactionServiceInterface;
use Financial\Transactions\Enums\TransactionEnum;

class TransferService extends BaseTransactionService implements TransactionServiceInterface
{
    /**
     * @throws Exception
     */
    public function handler(float $amount, Account $sender, Account|null $recipient = null): object
    {
        if ($sender->getAccountNumber() === $recipient->getAccountNumber()) {
            throw new Exception("You cannot transfer funds to yourself.");
        }

        $this->validateAmount($amount);

        $senderOldBalance = $this->checkFundSufficiencyAndReturnBalance($amount, $sender);

        $recipientOldBalance = $recipient->getAccountBalance();

        $senderNewBalance = $senderOldBalance - $amount;

        $recipientNewBalance = $recipientOldBalance + $amount;

        $payload = (object)[
            'id' => $this->generateTransactionId(),
            'comment' => "This is a transfer from {$sender->getAccountNumber()}'s account to {$recipient->getAccountNumber()}'s account",
            'amount' => $amount,
            'dueDate' => date("Y-m-d H:i:s"),
            'type' => $this->transactionType(),
            'sender' => $sender,
            'recipient' => $recipient,
            'sender_old_balance' => $senderOldBalance,
            'sender_new_balance' => $senderNewBalance,
            'recipient_old_balance' => $recipientOldBalance,
            'recipient_new_balance' => $recipientNewBalance,
        ];

        $sender->setAccountBalance($senderNewBalance);

        $recipient->setAccountBalance($recipientNewBalance);

        return $this->createTransaction($payload);
    }

    public function transactionType(): string
    {
        return TransactionEnum::TRANSFER;
    }
}