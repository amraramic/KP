# Banking system

The banking system is a small program developed for the purpose of illustrating object-oriented aspects of the PHP programming language.

The application is intended for the bank employee and contains the following functions:
- Create customer
- Create account (It distinguishes between a savings account, which is intended for saving and less for daily transactions, and a checking account, which is the usual current account. Both accounts have an interest rate that is added quarterly. Checking account has additionally the account maintenance fee that must be paid monthly.)
- Deposit money
- Debit money
- Perform transaction
- View account balance
- Pay compound interest
- debit account maintenance charge
- Delete customer
- Delete account
- View all accounts

# How to start the program
The application is operated via the Console and can be used from anywhere. 
To start the application the following steps should be performed:
- Install php (Version 8.1)
- Clone the repository to any directory
- Open command line
- Navigate to the directory of the cloned repository
- Execute php index.php

# Notes
- The paper (asciidoc and pdf) related to the project can be found in the docs folder
- Not much emphasis was placed on the user interface, since the focus was on object-oriented programming and not on the GUI
- Test coverage: 91% of the files coverage
  - Package exceptions: 100% Files coverage, 100% Lines coverage
  - Package src: 100% Files coverage, 95% Lines coverage


