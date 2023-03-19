<?php

namespace Financial\Transactions\Accounts;

use Exception;

class AccountManager
{
    private static $instance;

    private static array $accounts;

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

    public function getAllAccounts(): array
    {
        return self::$accounts;
    }

    /**
     * @throws Exception
     */
    public function getAccount(int $accountNumber): object
    {
        $account = $this->getAccountByIndex('number', $accountNumber);

        if (is_bool($account)) {
            throw new Exception('Account does not exist');
        }

        return self::$accounts[$account];
    }

    public function getAccountByIndex(string $column, string $value): bool|int
    {
        return array_search($value, array_column(self::$accounts, $column));
    }

    public function createAccount(string $fullname): object
    {
        $accountId = count(self::$accounts) + 1;
        $accountNumber = rand(1111111111, 9999999999);

        self::$accounts[] = (object)[
            'id' => $accountId,
            'name' => $fullname,
            'number' => $accountNumber
        ];

        $account = $this->getAccountByIndex('id', $accountId);

        return (object)self::$accounts[$account];
    }
}
