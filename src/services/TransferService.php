<?php

namespace Financial\Transactions\services;

use Exception;
use Financial\Transactions\Account;
use Financial\Transactions\contracts\TransactionServiceInterface;
use Financial\Transactions\Enums\TransactionEnum;
use Financial\Transactions\TransactionManager;

class TransferService extends TransactionManager implements TransactionServiceInterface
{
    /**
     * @throws Exception
     */
    public static function handler(float $amount, Account $sender, Account|null $recipient = null): object
    {
        if ($sender->getAccountNumber() === $recipient->getAccountNumber()) {
            throw new Exception("You cannot transfer funds to yourself.");
        }

        self::validateAmount($amount);

        $senderOldBalance = self::checkFundSufficiencyAndReturnBalance($amount, $sender);

        $recipientOldBalance = $recipient->getAccountBalance();

        $senderNewBalance = $senderOldBalance - $amount;

        $recipientNewBalance = $recipientOldBalance + $amount;

        $payload = (object)[
            'id' => self::generateTransactionId(),
            'comment' => "This is a transfer from {$sender->getAccountNumber()}'s account to {$recipient->getAccountNumber()}'s account",
            'amount' => $amount,
            'dueDate' => date("Y-m-d H:i:s"),
            'type' => self::transactionType(),
            'sender' => $sender->getAccountNumber(),
            'recipient' => $recipient->getAccountNumber(),
            'sender_old_balance' => $senderOldBalance,
            'sender_new_balance' => $senderNewBalance,
            'recipient_old_balance' => $recipientOldBalance,
            'recipient_new_balance' => $recipientNewBalance,
        ];

        $sender->setAccountBalance($senderNewBalance);

        $recipient->setAccountBalance($recipientNewBalance);

        return self::createTransaction($payload);
    }

    public static function transactionType(): string
    {
        return TransactionEnum::TRANSFER;
    }
}