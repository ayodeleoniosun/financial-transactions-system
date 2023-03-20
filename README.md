# Financial transaction system

### Overview of the application

This repository implements a set of classes for managing the financial operations of an account without MVC architecure,
any
framework and database.

There are three types of transactions: `deposits`, `withdrawals` and `transfer` from account to account.

Each transaction contains the following properties:

- Comment.
- Amount.
- Transaction type (either deposit, withdraw or transfer)
- Due date.
- Account number.
- Recipient account number (for transfer operations only).

Below are the features of this application:

- Create new account.
- Get single account details.
- Get all accounts in the system.
- Get balance of a specific account.
- Deposit to user account.
- Withdraw from user account.
- Transfer to another account.
- Sort user account transactions by comments in ascending and descending order.
- Sort user account transactions by due date in ascending and descending order.

### Software development patterns and principles

The singleton and factory pattern are used in this application.

The following classes handle the financial transactions:

`AccountManager`: Singleton design pattern is used in this class to create just one instance of the class and
then reuse the created instance subsequently.

It handles the following account management operations:

- Create account.
- Get a single account.
- Get all accounts.

This obeys the `information expert` section of GRASP principles.

`Account`: This handles the following transaction operations:

- Create new transactions.
- Get user account balance.
- Get user transactions.

`BaseTransaction`: This is an abstract class for encapsulating common properties and methods for all
transaction types.

Three transaction classes - `Deposit`, `Withdraw` and `Transfer` extend the `BaseTransaction` class and implements
the `handle` abstract method.
This obeys the `encapsulation` and `inheritance` attributes of OOP.

`TransactionFactory`: A `factory` pattern is used in this class to dynamically instantiate the child classes
that extends the `BaseTransaction` parent class.

`FilterTransactions`: This class handles the sorting for transactions by due date and comments both in ascending and
descending order.

`FinancialTransactions` class manually tests all the above mentioned features, although tests were written to automate
that.

### Testing

Tests were written via PHPUnit with a good test coverage.

Run the following command to run tests:

```shell
./vendor/bin/phpunit
```

