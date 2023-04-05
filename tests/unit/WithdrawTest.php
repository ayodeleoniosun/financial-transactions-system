<?php

namespace Financial\Transactions\Tests;

use Exception;
use Financial\Transactions\Account;
use Financial\Transactions\Enums\TransactionEnum;
use Financial\Transactions\services\WithdrawalService;
use PHPUnit\Framework\TestCase;

final class WithdrawTest extends TestCase
{
    protected WithdrawalService $withdrawalService;
    protected Account $accountManager;
    protected Account $account;

    public function setUp(): void
    {
        $this->withdrawalService = new WithdrawalService();
        $this->accountManager = Account::getInstance();
        $this->account = $this->accountManager->createAccount('Nameless User');
    }


    /**
     * @throws Exception
     */
    public function test_cannot_withdraw_an_invalid_amount()
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage("Invalid amount. Try again");

        $this->withdrawalService::handler(0, $this->account);
    }

    /**
     * @throws Exception
     */
    public function test_cannot_withdraw_from_a_low_account_balance()
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage("Insufficient fund");

        $this->withdrawalService::handler(1000, $this->account);
    }

    /**
     * @throws Exception
     */
    public function test_can_withdraw()
    {
        $this->account->setAccountBalance(3000);

        $withdraw = $this->withdrawalService::handler(2000, $this->account);

        $this->assertEquals(2000, $withdraw->amount);
        $this->assertEquals(TransactionEnum::WITHDRAW, $withdraw->type);
        $this->assertNull($withdraw->sender);
        $this->assertEquals($this->account->getAccountNumber(), $withdraw->recipient);
        $this->assertEquals(3000, $withdraw->old_balance);
        $this->assertEquals(1000, $withdraw->new_balance);
    }
}
