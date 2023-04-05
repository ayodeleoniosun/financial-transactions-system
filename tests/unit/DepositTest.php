<?php

namespace Financial\Transactions\Tests;

use Exception;
use Financial\Transactions\Account;
use Financial\Transactions\Enums\TransactionEnum;
use Financial\Transactions\services\DepositService;
use PHPUnit\Framework\TestCase;

final class DepositTest extends TestCase
{
    protected DepositService $depositService;
    protected Account $accountManager;
    protected Account $account;

    public function setUp(): void
    {
        $this->depositService = new DepositService();
        $this->accountManager = Account::getInstance();
        $this->account = $this->accountManager->createAccount('Nameless User');
    }

    /**
     * @throws Exception
     */
    public function test_cannot_deposit_an_invalid_amount()
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage("Invalid amount. Try again");

        $this->depositService::handler(0, $this->account);
    }

    /**
     * @throws Exception
     */
    public function test_can_deposit()
    {
        $deposit = $this->depositService::handler(1000, $this->account);

        $this->assertEquals(1000, $deposit->amount);
        $this->assertEquals(TransactionEnum::DEPOSIT, $deposit->type);
        $this->assertNull($deposit->sender);
        $this->assertEquals($this->account->getAccountNumber(), $deposit->recipient);
        $this->assertEquals(0, $deposit->old_balance);
        $this->assertEquals(1000, $deposit->new_balance);
    }
}
