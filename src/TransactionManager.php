<?php

namespace Financial\Transactions;

use Exception;

class TransactionManager
{
    protected static $instance;

    protected static array $transactions;

    public function __construct()
    {
        self::$transactions = [];
    }

    public static function getInstance(): self
    {
        if (!self::$instance) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    public static function createTransaction($payload): object
    {
        $payload->id = self::generateTransactionId();

        self::$transactions[$payload->id] = $payload;

        return self::$transactions[$payload->id];
    }

    public static function generateTransactionId(): int
    {
        return count(self::getTransactions()) + 1;
    }

    public static function getTransactions(): array
    {
        return self::$transactions;
    }

    /**
     * @throws Exception
     */
    public static function checkFundSufficiencyAndReturnBalance(float $amount, Account $account): float
    {
        self::validateAmount($amount);

        $balance = $account->getAccountBalance();

        if ($balance < $amount) {
            throw new Exception("Insufficient fund");
        }

        return $balance;
    }

    /**
     * @throws Exception
     */
    public static function validateAmount(float $amount): float
    {
        if ($amount < 1) {
            throw new Exception("Invalid amount. Try again");
        }

        return $amount;
    }

    public static function getAccountTransactions(Account $account): array
    {
        return array_values(array_filter(self::getTransactions(), function ($transaction) use ($account) {
            return $transaction->sender === $account->getAccountNumber() || $transaction->recipient === $account->getAccountNumber();
        }));
    }

    /**
     * @throws Exception
     */
    public function execute(string $transactionType, float $amount, Account $sender, Account|null $recipient = null): object
    {
        return $transactionType::handler($amount, $sender, $recipient);
    }
}
