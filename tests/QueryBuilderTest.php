<?php
/**
 * Copyright (c) 2020.
 * Designed and developed by Aleksandr Dremov
 * Use according to the license guidelines.
 * Contact me: dremov.me@gmail.com
 */

use DAIM\Core\Connection;
use DAIM\Core\Credentials;
use DAIM\Core\QueryBuilder;
use DAIM\Syntax\SQLEntities\Conditions;
use PHPUnit\Framework\TestCase;

/**
 * Copyright (c) 2020.
 * Designed and developed by Aleksandr Dremov
 * Use according to the license guidelines.
 * Contact me: dremov.me@gmail.com
 */
class QueryBuilderTest extends TestCase
{
    private $host = '127.0.0.1';

    private $username = 'test';

    private $Dbname = 'daim';

    private $password = 'qwerty123';

    private $port = 3306;

    private $dbUpdated = false;

    public function setUp()
    {
        $cred = new Credentials();
        $cred->setHost($this->host);
        $cred->setUsername($this->username);
        $cred->setDbname($this->Dbname);
        $cred->setPassword($this->password);
        $cred->setPort($this->port);
        Connection::setCredentials($cred);
        Connection::initConnection();
        if (!$this->dbUpdated) {
            Connection::multiQuery(file_get_contents(__DIR__ . '/../sql-data/data.sql'));
            $this->dbUpdated = !$this->dbUpdated;
        }
    }

    public function testBuilderSelect()
    {

        $method = new ReflectionMethod('DAIM\Core\QueryBuilder', 'generateQueryString');
        $method->setAccessible(true);

        $qb = new QueryBuilder();

        $cond = new Conditions();
        $cond->field('LastName')->equal()->value('foo')->and()->field('PersonID')->equal()->value('1');

        $qb->select()->all()->from()->tableName('Persons')->where()->conditions($cond);
        $this->assertEquals($method->invokeArgs($qb, []), "SELECT * FROM Persons WHERE LastName = 'foo' AND PersonID = '1'");

        $qb->clear();

        $qb->select()->star()->from()->tableName('Persons')->where()->conditions($cond);
        $this->assertEquals($method->invokeArgs($qb, []), "SELECT * FROM Persons WHERE LastName = 'foo' AND PersonID = '1'");

        $qb->clear();
        $qb->select('*')->from()->tableName('Persons')->where()->conditions($cond);
        $this->assertEquals($method->invokeArgs($qb, []), "SELECT * FROM Persons WHERE LastName = 'foo' AND PersonID = '1'");

        $qb->clear();
        $qb->select('*')->from('Persons')->where()->conditions($cond);
        $this->assertEquals($method->invokeArgs($qb, []), "SELECT * FROM Persons WHERE LastName = 'foo' AND PersonID = '1'");

        $qb->clear();
        $qb->select();
        $this->assertEquals($method->invokeArgs($qb, []), "SELECT");

        $qb->clear();

        $qb->select('LastName', 'PersonID');
        $this->assertEquals($method->invokeArgs($qb, []), "SELECT LastName, PersonID");

        $qb->clear();

        $qb->select()->
        columns('Persons.LastName', 'Persons.PersonID', 'Information.Tel')->
        from()->tableNames('Information', 'Persons')->
        where(
            (new Conditions())->field('Information.PersonID')->equal()->field('Persons.PersonID')
        );
        $this->assertEquals(
            $method->invokeArgs($qb, []),
            "SELECT Persons.LastName, Persons.PersonID, Information.Tel FROM Information, Persons WHERE Information.PersonID = Persons.PersonID");
    }

}