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

`TransactionManager`: This class handles all the transaction operations - deposits, withdrawals and transfers.

`Ledger`: This class handles the retrieval of different types of transactions - deposits, withdrawals and transfers, as
well as the sorting for transactions by due date and comments both in ascending and
descending order.

`TestApp` class manually tests all the above mentioned features, although tests were written to automate
that.

GRASP principles such as `information expert` is used to assign responsibility to the class that has the information
needed to fulfill it, as each class does one major thing and not a myriad of unrelated operations.

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

