<?php

namespace Financial\Transactions;

use Exception;

class Account
{
    private static $instance;

    private static array $accounts;

    protected int $id;

    protected string $name;

    protected int $accountNumber;

    protected float $balance;

    private function __construct()
    {
        self::$accounts = [];
    }

    public static function getInstance(): self
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getAccountNumber(): int
    {
        return $this->accountNumber;
    }

    public function getAccountBalance(): float
    {
        return $this->balance;
    }

    public function getAllAccounts(): array
    {
        return self::$accounts;
    }
    
    /**
     * @throws Exception
     */
    public function setAccountBalance(int $accountNumber, float $balance): void
    {
        $account = $this->getAccount($accountNumber);

        $account->balance = $balance;
    }

    /**
     * @throws Exception
     */
    public function getAccount(int $accountNumber): object
    {
        $isValidAccountNumber = strlen($accountNumber) === 11;

        if (!$isValidAccountNumber) {
            throw new Exception('Invalid account number.');
        }

        $account = self::$accounts[$accountNumber] ?? null;

        if (!$account) {
            throw new Exception('Account does not exist.');
        }

        return $account;
    }

    public function createAccount(string $fullname): object
    {
        $accountId = count(self::$accounts) + 1;

        $accountNumber = rand(11111111111, 99999999999); // account number must be of 11 numeric characters

        $account = new self();
        $account->id = $accountId;
        $account->accountNumber = $accountNumber;
        $account->name = $fullname;
        $account->balance = 0.0;

        self::$accounts[$accountNumber] = $account;

        return self::$accounts[$accountNumber];
    }
}
