# Financial transaction system

### Overview of the application

This repository implements a set of classes for managing the financial operations of an account without MVC architecure,
any
framework and database.

There are three types of transactions: `deposits`, `withdrawals` and `transfer` from account to account.

Each transaction contains the following properties:

- id
- Comment.
- Amount.
- Transaction type (either deposit, withdraw or transfer)
- Due date.
- Sender (account number or null for deposit and withdrawal operations).
- Recipient (account number).
- Old account balance.
- New account balance.

Below are the features of this application:

- Create new account.
- Get single account details.
- Get all accounts in the system.
- Get balance of a specific account.
- Deposit to a user account.
- Withdraw from user account.
- Transfer from one account to another account.
- Sort user account transactions by comments in ascending and descending order.
- Sort user account transactions by due date in ascending and descending order.

### Software development patterns and principles

The following classes handle the financial transactions:

`Account`: Singleton design pattern is used in this class to create just one instance of the class and
then reuse the created instance subsequently.

This pattern also makes it easy to retrieve all the created user accounts.

It handles everything related to account management:

- Create account.
- Get a single account.
- Get user account balance.
- Get all accounts.

`TransactionManager`: This class serves as a base class for all validation checks in transaction operations services -
deposits, withdrawals and
transfers.

`DepositService`: This class contains the logic for deposit operation.

`WithdrawalService`: This class contains the logic for withdrawal operation.

`TransferService`: This class contains the logic for transfer operation.

`Ledger`: This class handles the retrieval of different types of transactions - deposits, withdrawals and transfers, as
well as the sorting for transactions by due date and comments both in ascending and
descending order.

`TestApp` class manually tests all the above mentioned features, although tests were written to automate
that.

SOLID principle was made use of in the following ways:

1. Ensuring that each class is cohesive and does only closely related things and not a myriad of unrelated operations -
   Single Responsibility.
2. Implementation of services for each transaction operation and additional transaction operation can be added in the
   future without touching the `TransactionManager` class - Open for extension, Close for Modification.
3. Usage of Interfaces and its implementation in each transaction service class - Interface Segregation.

### Installation guides

You must have `php` and `composer` installed on your machine and the php version requirement is `^8.1`

#### Step 1: Clone the repository

```shell
git clone https://github.com/ayodeleoniosun/financial-transactions-system.git
```

#### Step 2: Switch to the repo folder

```shell
cd financial-transactions-system
```

#### Step 3: Install composer packages

```shell
composer install
```

### Testing

Tests were written via PHPUnit with a good test coverage.

Run the following command to run tests:

```shell
./vendor/bin/phpunit
```

