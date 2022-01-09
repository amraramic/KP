Banking system

The banking system is a small program developed for the purpose of illustrating object-oriented aspects of the PHP programming language.

The application is intended for the bank employee and contains the following functions:
- Create customer
- Create account
It distinguishes between a savings account, which is intended for saving and less for daily transactions, and a checking account, which is the usual current account. Both accounts have an interest rate that is added quarterly. Checking account has additionally the account maintenance fee that must be paid monthly.
- Deposit money
- Debit money
- Perform transaction
- View account balance
- Pay compound interest
- debit account maintenance charge
- Delete customer
- Delete account
- View all accounts

The application is operated via the Console and can be used from anywhere. 
To start the application the following steps should be performed:
- Clone the repository to any directory
- Open command line
- Navigate to the directory of the cloned repository
- execute php index.php

Notes:
- Not much emphasis was placed on the user interface, since the focus was on object-oriented programming and not on the GUI
- Test coverage: 91% of the files coverage
- Package exceptions: 100% Files coverage, 100% Lines coverage
- Package src: 100% Files coverage, 78% Lines coverage (because some methods were not tested twice e.g. doDebit() and debit())


