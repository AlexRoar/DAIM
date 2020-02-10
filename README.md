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

## Installation

Just use [composer](https://getcomposer.org).
```bash
composer require alexdremov/daim
```

## Beta: what's ready?

You can use it for keeping your Database connections organized and single.
At first, you need to setup the framework:
```php
use DAIM\Core\Connection;
use DAIM\Core\Credentials;

$cred = new Credentials();
$cred->setHost(host);
$cred->setUsername(username);
$cred->setDBname(DBname);
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
```

Now we are ready to go.
```php
// Basic raw query.
Connection::query('SELECT * FROM `Persons` WHERE 1', "secondConnectionName");
```

Currently, I am working on Query Builder. Some features are already available:

SELECT:
```php
use DAIM\Core\QueryBuilder;
use DAIM\Syntax\SQLEntities\Conditions;

$qb = new QueryBuilder();
$result = $qb->select('*')->from('Information')->request();

# Or more complicated usage:

$result = $qb->select(
    'Persons.LastName', 'Persons.PersonID', 'Information.Tel'
)->from(
    'Information', 'Persons'
)->where(
    (new Conditions())->field('Information.PersonID')->equal()->field('Persons.PersonID')
)->request();

# Generates SQL
# SELECT Persons.LastName, Persons.PersonID, Information.Tel FROM Information, Persons WHERE Information.PersonID = Persons.PersonID
# final ->request() returns instance of QueryResult class.
```
INSERT:
```php
use DAIM\Core\QueryBuilder;

$qb = new QueryBuilder();

$qb->insertInto('tableName', array(
"field1"=>"value1",
"field2"=>"value2"
));
$response = $qb->request(); // Commit changes

// Or longer version:

$qb->insertInto('tableName')->columns('field1', 'field2', 'field3')->values('value1', 'value2', 'value3')->request();
$qb->insertInto('tableName')->values('value1', 'value2', 'value3')->request();
```
Subqueries are also available in INSERT builder.

Such limited library usage is due to the beta status of the project.
