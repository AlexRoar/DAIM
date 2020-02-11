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
use DAIM\Exceptions\ConnectionException;
use DAIM\Exceptions\CredentialsException;
use DAIM\Exceptions\MySQLSyntaxException;
use DAIM\Exceptions\QueryBuilderException;
use DAIM\Exceptions\QueryPathException;
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

    /**
     * @var bool
     */
    private $dbUpdated = false;

    /**
     * QueryBuilderTest constructor.
     * @param null $name
     * @param array $data
     * @param string $dataName
     */
    public function __construct($name = null, array $data = [], $dataName = '')
    {
        parent::__construct($name, $data, $dataName);
        $envVars = json_decode(file_get_contents(__DIR__ . '/environment.json'), true);
        foreach ($envVars as $key => $value) {
            $this->$key = $value;
        }
        foreach ($envVars['database'] as $key => $value) {
            $this->$key = $value;
        }
    }

    /**
     * @throws ConnectionException
     * @throws CredentialsException
     */
    public function setUp()
    {
        $cred = new Credentials();
        $cred->setHost($this->host);
        $cred->setUsername($this->username);
        $cred->setDBname($this->DBname);
        $cred->setPassword($this->password);
        $cred->setPort($this->port);
        Connection::setCredentials($cred);
        Connection::initConnection();
        if (!$this->dbUpdated) {
            Connection::multiQuery(file_get_contents(__DIR__ . '/../sql-data/data.sql'));
            $this->dbUpdated = !$this->dbUpdated;
        }
    }

    /**
     * @throws ReflectionException
     * @throws ConnectionException
     * @throws CredentialsException
     * @throws MySQLSyntaxException
     * @throws QueryBuilderException
     * @throws QueryPathException
     */
    public function testBuilderSelect()
    {

        $method = new ReflectionMethod('DAIM\Core\QueryBuilder', 'generateQueryString');
        $method->setAccessible(true);

        $qb = new QueryBuilder();


        $qb->select()->all()->from()->tableName('Persons')->where()->conditions(
            $qb->createCondition()->field('LastName')->equal()->val('foo')->and()->field('PersonID')->equal()->val('1')
        );
        $this->assertEquals($method->invokeArgs($qb, []), "SELECT * FROM Persons WHERE LastName = 'foo' AND PersonID = '1'");

        $qb->clear();
        $cond = new Conditions();
        $cond->field('LastName')->equal()->val('foo')->and()->field('PersonID')->equal('1');

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

        $qb->clear();

        $response = $qb->select('PersonID', 'LastName')->from('Persons')->where(
            $qb->createCondition()->true()
        )->request();

        $this->assertEquals(2, $response->getRowsNumber());
    }

    /**
     * @throws MySQLSyntaxException
     * @throws QueryBuilderException
     * @throws QueryPathException
     * @throws ReflectionException
     * @depends testBuilderSelect
     */
    public function testBuilderInsert()
    {
        $method = new ReflectionMethod('DAIM\Core\QueryBuilder', 'generateQueryString');
        $method->setAccessible(true);

        $qb = new QueryBuilder();
        $response = $qb->select('*')
            ->from('Persons')
            ->where($qb->createCondition()->field('PersonID')->equal('1212'))->request();

        $this->assertEquals(0, $response->getRowsNumber());
        $qb = $qb->insertInto('Persons')->columns('PersonID', 'LastName', 'FirstName', 'Address', 'City');

        $this->assertEquals($method->invokeArgs($qb, []), "INSERT INTO Persons ( PersonID, LastName, FirstName, Address, City )");

        $qb = $qb->values(1212, "asf'", "Bad", "Nowhere", "Moscow");
        $this->assertEquals($method->invokeArgs($qb, []), "INSERT INTO Persons ( PersonID, LastName, FirstName, Address, City ) VALUES ('1212', 'asf\'', 'Bad', 'Nowhere', 'Moscow')");
        $qb->request();


        $response = $qb->select('*')
            ->from('Persons')
            ->where($qb->createCondition()->field('PersonID')->equal('1212'))->request();
        $values = $response->fetchAssoc();

        $this->assertEquals(1, $response->getRowsNumber());
        $this->assertEquals(1212, $values['PersonID']);
        $this->assertEquals('asf\'', $values['LastName']);
        $this->assertEquals('Bad', $values['FirstName']);
        $this->assertEquals('Nowhere', $values['Address']);
        $this->assertEquals('Moscow', $values['City']);
    }

    /**
     * @throws MySQLSyntaxException
     * @throws QueryBuilderException
     * @throws QueryPathException
     * @depends testBuilderInsert
     */
    public function testBuilderInsertArray()
    {
        $qb = new QueryBuilder();

        $response = $qb->insertInto('Persons', array(
            'PersonID' => 1212,
            'LastName' => 'asf\'',
            'FirstName' => 'Bad',
            'Address' => 'Nowhere',
            'City' => 'Moscow'
        ))->request();

        $response = $qb->select('*')
            ->from('Persons')
            ->where($qb->createCondition()->field('PersonID')->equal('1212'))->request();
        $values = $response->fetchAssoc();

        $this->assertEquals(1, $response->getRowsNumber());
        $this->assertEquals(1212, $values['PersonID']);
        $this->assertEquals('asf\'', $values['LastName']);
        $this->assertEquals('Bad', $values['FirstName']);
        $this->assertEquals('Nowhere', $values['Address']);
        $this->assertEquals('Moscow', $values['City']);
    }

    /**
     * @throws MySQLSyntaxException
     * @throws QueryBuilderException
     * @throws QueryPathException
     * @depends testBuilderInsert
     */
    public function testBuilderInsertMini()
    {
        $qb = new QueryBuilder();
        $qb->insertInto('Persons')->values('1222', 'Dremov', 'Alex', 'Nowhere', 'Moscow')->request();

        $response = $qb->select('*')
            ->from('Persons')
            ->where($qb->createCondition()->field('PersonID')->equal('1222'))->request();
        $values = $response->fetchAssoc();

        $this->assertEquals(1, $response->getRowsNumber());
        $this->assertEquals(1222, $values['PersonID']);
        $this->assertEquals('Dremov', $values['LastName']);
        $this->assertEquals('Alex', $values['FirstName']);
        $this->assertEquals('Nowhere', $values['Address']);
        $this->assertEquals('Moscow', $values['City']);
    }

    /**
     * @depends testBuilderInsert
     * @depends testBuilderSelect
     * @throws QueryPathException
     * @throws MySQLSyntaxException
     * @throws QueryBuilderException
     */
    public function testBuilderDeleteStatement()
    {
        $method = new ReflectionMethod('DAIM\Core\QueryBuilder', 'generateQueryString');
        $method->setAccessible(true);

        $qb = new QueryBuilder();
        $qb->insertInto('Persons')->values('1222', 'Dremov', 'Alex', 'Nowhere', 'Moscow')->request();
        $qb->insertInto('Persons')->values('1223', 'Dremov2', 'Alex2', 'Nowhere2', 'Moscow2')->request();

        $response = $qb->select('*')
            ->from('Persons')
            ->where($qb->createCondition()->field('PersonID')->equal('1222'))->request();

        $this->assertEquals(1, $response->getRowsNumber());

        $response = $qb->select('*')
            ->from('Persons')
            ->where($qb->createCondition()->field('PersonID')->equal('1223'))->request();

        $this->assertEquals(1, $response->getRowsNumber());

        $qb->delete()->from('Persons')->where($qb->createCondition()->field('PersonID')->equal('1222'));

        $this->assertEquals($method->invokeArgs($qb, []), "DELETE FROM Persons WHERE PersonID = '1222'");
        $qb->request();


        $response = $qb->select('*')
            ->from('Persons')
            ->where($qb->createCondition()->field('PersonID')->equal('1222'))->request();

        $this->assertEquals(0, $response->getRowsNumber());

        $response = $qb->select('*')
            ->from('Persons')
            ->where($qb->createCondition()->field('PersonID')->equal('1223'))->request();

        $this->assertEquals(1, $response->getRowsNumber());
    }

}