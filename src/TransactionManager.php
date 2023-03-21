<?php

namespace Financial\Transactions;

use Exception;
use Financial\Transactions\Enums\TransactionEnum;

class TransactionManager
{
    protected array $transactions;

    public function __construct()
    {
        $this->transactions = [];
    }

    /**
     * @throws Exception
     */
    public function deposit(float $amount, int $recipient): object
    {
        $account = $this->validateAccount($recipient);

        $this->validateAmount($amount);

        $oldBalance = $account->getAccountBalance();

        $newBalance = $oldBalance + $amount;

        $payload = (object)[
            'id' => $this->generateTransactionId(),
            'comment' => "This is a deposit into {$recipient}'s account",
            'amount' => $amount,
            'dueDate' => date("Y-m-d H:i:s"),
            'type' => TransactionEnum::DEPOSIT,
            'sender' => null,
            'recipient' => $recipient,
            'old_balance' => $oldBalance,
            'new_balance' => $newBalance,
        ];

        $account->setAccountBalance($newBalance);

        return $this->createTransaction($payload);
    }

    /**
     * @throws Exception
     */
    public function validateAccount(int $accountNumber): object
    {
        $account = Account::getInstance();

        return $account->getAccount($accountNumber);
    }

    /**
     * @throws Exception
     */
    public function validateAmount(float $amount): float
    {
        if ($amount < 1) {
            throw new Exception("Invalid amount. Try again");
        }

        return $amount;
    }

    public function generateTransactionId(): int
    {
        return count($this->getTransactions()) + 1;
    }

    public function getTransactions(): array
    {
        return $this->transactions;
    }

    public function createTransaction($transaction): object
    {
        $this->transactions[$transaction->id] = $transaction;

        return $this->transactions[$transaction->id];
    }

    public function getAccountTransactions(Account $account): array
    {
        return array_values(array_filter($this->transactions, function ($transaction) use ($account) {
            return $transaction->sender === $account->getAccountNumber() || $transaction->recipient === $account->getAccountNumber();
        }));
    }

    /**
     * @throws Exception
     */
    public function withdraw(float $amount, int $accountNumber): object
    {
        $account = $this->validateAccount($accountNumber);

        $this->validateAmount($amount);

        $oldBalance = $account->getAccountBalance();

        if ($oldBalance < $amount) {
            throw new Exception("Insufficient fund");
        }

        $newBalance = $oldBalance - $amount;

        $payload = (object)[
            'id' => $this->generateTransactionId(),
            'comment' => "This is a withdrawal from {$accountNumber}'s account",
            'amount' => $amount,
            'dueDate' => date("Y-m-d H:i:s"),
            'type' => TransactionEnum::WITHDRAW,
            'sender' => null,
            'recipient' => $accountNumber,
            'old_balance' => $oldBalance,
            'new_balance' => $newBalance,
        ];

        $account->setAccountBalance($newBalance);

        return $this->createTransaction($payload);
    }

    /**
     * @throws Exception
     */
    public function transfer(float $amount, int $sender, int $recipient): object
    {
        $getSender = $this->validateAccount($sender);

        $getRecipient = $this->validateAccount($recipient);

        if ($getSender->getAccountNumber() == $getRecipient->getAccountNumber()) {
            throw new Exception("You cannot transfer funds to yourself.");
        }

        $this->validateAmount($amount);

        $senderOldBalance = $getSender->getAccountBalance();

        if ($senderOldBalance < $amount) {
            throw new Exception("Insufficient fund");
        }

        $recipientOldBalance = $getRecipient->getAccountBalance();

        $senderNewBalance = $senderOldBalance - $amount;

        $recipientNewBalance = $recipientOldBalance + $amount;

        $payload = (object)[
            'id' => $this->generateTransactionId(),
            'comment' => "This is a transfer from {$sender}'s account to {$recipient}'s account",
            'amount' => $amount,
            'dueDate' => date("Y-m-d H:i:s"),
            'type' => TransactionEnum::TRANSFER,
            'sender' => $sender,
            'recipient' => $recipient,
            'sender_old_balance' => $senderOldBalance,
            'sender_new_balance' => $senderNewBalance,
            'recipient_old_balance' => $recipientOldBalance,
            'recipient_new_balance' => $recipientNewBalance,
        ];

        $getSender->setAccountBalance($senderNewBalance);

        $getRecipient->setAccountBalance($recipientNewBalance);

        return $this->createTransaction($payload);
    }
}
