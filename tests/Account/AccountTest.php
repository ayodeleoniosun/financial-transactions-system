<?php

namespace Financial\Transactions\Tests\Account;

require_once "vendor/autoload.php";

use Faker\Factory;
use Faker\Generator as Faker;
use Financial\Transactions\Accounts\Account;
use Financial\Transactions\Enums\TransactionEnum;
use PHPUnit\Framework\TestCase;

final class AccountTest extends TestCase
{
    protected int $accountNumber;
    protected Account $account;
    protected Faker $faker;

    public function setUp(): void
    {
        $this->faker = Factory::create();
        $this->accountNumber = $this->faker->randomNumber(9);
        $this->account = new Account($this->accountNumber);
    }

    public function test_can_create_deposit_transactions()
    {
        $payload = $this->transactionPayload(TransactionEnum::DEPOSIT);

        $deposit = $this->account->createTransaction($payload);

        $this->assertEquals($deposit->accountNumber, $payload['accountNumber']);
        $this->assertEquals($deposit->type, $payload['type']);
        $this->assertEquals($deposit->amount, $payload['amount']);
        $this->assertEquals($deposit->comment, $payload['comment']);
        $this->assertEquals($deposit->dueDate, $payload['dueDate']);
        $this->assertNull($deposit->recipient);
    }

    public function transactionPayload(string $type, int|null $recipient = null): array
    {
        return [
            'accountNumber' => $this->accountNumber,
            'type' => $type,
            'amount' => $this->faker->randomFloat(3, 1000, 999999),
            'comment' => $this->faker->text(50),
            'dueDate' => date('Y-m-d H:i:s'),
            'recipient' => ($type === TransactionEnum::TRANSFER) ? $this->faker->randomNumber(9) : null
        ];
    }

    public function test_can_create_withdrawal_transactions()
    {
        $payload = $this->transactionPayload(TransactionEnum::WITHDRAW);

        $withdraw = $this->account->createTransaction($payload);

        $this->assertEquals($withdraw->accountNumber, $payload['accountNumber']);
        $this->assertEquals($withdraw->type, $payload['type']);
        $this->assertEquals($withdraw->amount, $payload['amount']);
        $this->assertEquals($withdraw->comment, $payload['comment']);
        $this->assertEquals($withdraw->dueDate, $payload['dueDate']);
        $this->assertNull($withdraw->recipient);
    }

    public function test_can_create_transfer_transactions()
    {
        $payload = $this->transactionPayload(TransactionEnum::TRANSFER);

        $withdraw = $this->account->createTransaction($payload);

        $this->assertEquals($withdraw->accountNumber, $payload['accountNumber']);
        $this->assertEquals($withdraw->type, $payload['type']);
        $this->assertEquals($withdraw->amount, $payload['amount']);
        $this->assertEquals($withdraw->comment, $payload['comment']);
        $this->assertEquals($withdraw->dueDate, $payload['dueDate']);
        $this->assertEquals($withdraw->recipient, $payload['recipient']);
        $this->assertNotNull($withdraw->recipient);
    }

    public function test_can_get_account_balance()
    {
        $this->createTransactions();

        $balance = $this->account->getAccountBalance();

        $this->assertEquals(9000, $balance);
    }

    public function createTransactions()
    {
        // create 3 deposits
        $depositPayload = $this->transactionPayload(TransactionEnum::DEPOSIT);
        $depositPayload['amount'] = 4000;
        $this->account->createTransaction($depositPayload);

        $depositPayload['amount'] = 5000;
        $this->account->createTransaction($depositPayload);

        $depositPayload['amount'] = 6000;
        $this->account->createTransaction($depositPayload);

        // create 2 withdrawals
        $withdrawalPayload = $this->transactionPayload(TransactionEnum::WITHDRAW);
        $withdrawalPayload['amount'] = 2000;
        $this->account->createTransaction($withdrawalPayload);

        $withdrawalPayload['amount'] = 1000;
        $this->account->createTransaction($withdrawalPayload);

        // create 1 transfer
        $transferPayload = $this->transactionPayload(TransactionEnum::WITHDRAW);
        $transferPayload['amount'] = 3000;
        $this->account->createTransaction($transferPayload);
    }

    public function test_can_get_deposit_transactions()
    {
        // create 3 deposits
        $depositPayload = $this->transactionPayload(TransactionEnum::DEPOSIT);
        $depositPayload['amount'] = 4000;
        $this->account->createTransaction($depositPayload);

        $depositPayload['amount'] = 5000;
        $this->account->createTransaction($depositPayload);

        $depositPayload['amount'] = 6000;
        $this->account->createTransaction($depositPayload);

        $deposits = $this->account->getDepositTransactions();

        $this->assertCount(3, $deposits);
        $this->assertEquals($deposits[0]->amount, 4000);
        $this->assertEquals($deposits[1]->amount, 5000);
        $this->assertEquals($deposits[2]->amount, 6000);
    }

    public function test_can_get_withdrawal_transactions()
    {
        // create 3 withdrawals
        $withdrawalPayload = $this->transactionPayload(TransactionEnum::WITHDRAW);
        $withdrawalPayload['amount'] = 2000;
        $this->account->createTransaction($withdrawalPayload);

        $withdrawalPayload['amount'] = 1000;
        $this->account->createTransaction($withdrawalPayload);

        $withdrawalPayload['amount'] = 500;
        $this->account->createTransaction($withdrawalPayload);

        $withdrawals = $this->account->getWithdrawalTransactions();

        $this->assertCount(3, $withdrawals);
        $this->assertEquals($withdrawals[0]->amount, 2000);
        $this->assertEquals($withdrawals[1]->amount, 1000);
        $this->assertEquals($withdrawals[2]->amount, 500);
    }

    public function test_can_get_transfer_transactions()
    {
        // create 3 transfers
        $transferPayload = $this->transactionPayload(TransactionEnum::TRANSFER);
        $transferPayload['amount'] = 3500;
        $this->account->createTransaction($transferPayload);

        $transferPayload['amount'] = 1500;
        $this->account->createTransaction($transferPayload);

        $transferPayload['amount'] = 2000;
        $this->account->createTransaction($transferPayload);

        $transfers = $this->account->getTransferTransactions();

        $this->assertCount(3, $transfers);
        $this->assertEquals($transfers[0]->amount, 3500);
        $this->assertEquals($transfers[1]->amount, 1500);
        $this->assertEquals($transfers[2]->amount, 2000);
        $this->assertNotNull($transfers[0]->recipient);
        $this->assertNotNull($transfers[1]->recipient);
        $this->assertNotNull($transfers[2]->recipient);
    }

    public function test_can_get_account_transactions()
    {
        $this->createTransactions();

        $transactions = $this->account->getAccountTransactions();

        $this->assertCount(6, $transactions);
    }
}