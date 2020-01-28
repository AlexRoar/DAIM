# DAIM
![](https://img.shields.io/badge/status-beta-red)
![](https://img.shields.io/circleci/build/github/AlexRoar/DAIM/master)
![](https://img.shields.io/github/repo-size/AlexRoar/DAIM)
![](https://img.shields.io/github/last-commit/AlexRoar/DAIM)

The framework is designed to minimize usage of SQL code in interactions between MySQL server and PHP scripts and to bind PHP object alterations with DB alterations.

## Why it is gorgeous?

- Database reactive tables models. You change PHP model — Database changes automatically.
- Generates PHP classes according to existing tables, helps your IDE with suggestions.
- Keeps connections efficient, uses only one active MySQL connection through whole request.
- Keeps connections to multiple databases organized.
- Automatic prevention of SQL-injections.

## Beta: what's ready?

You can use it for keeping your Database connection
```php
use DAIM\Core\Connection;
use DAIM\Core\Credentials;
    
$cred = new Credentials();
$cred->setHost(host);
$cred->setUsername(username);
$cred->setDbname(DBname);
$cred->setPassword(password);
$cred->setPort(port);

Connection::setCredentials($cred);
Connection::initConnection();
   
Connection::getConnection(); # returns active MySQL connection (instance of mysqli class);

# To set up a second connection (maybe to the second database),
# you can create additional connection mode:

/**
 * Set up $cred2 as instance of Credentials class for the second connection
 * @var $cred2 Credentials;
 */

Connection::setCredentials($cred2, "secondConnectionName");
Connection::initConnection("secondConnectionName");
    
Connection::getConnection("secondConnectionName");
```
