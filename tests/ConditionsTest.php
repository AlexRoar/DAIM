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
class ConditionsTest extends TestCase
{
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
     * ConditionsTest constructor.
     * @param null $name
     * @param array $data
     * @param string $dataName
     * @throws ConnectionException
     * @throws CredentialsException
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
        $cred = new Credentials();
        $cred->setHost($this->host);
        $cred->setUsername($this->username);
        $cred->setDBname($this->DBname);
        $cred->setPassword($this->password);
        $cred->setPort($this->port);
        Connection::setCredentials($cred);
        Connection::initConnection();
    }

    /**
     * @throws MySQLSyntaxException
     */
    public function testBasicOperations()
    {
        $cond = new Conditions();

        $this->assertEquals('column = \'value\'', (string)$cond->field('column')->equal('value'));
        $cond->clear();

        $this->assertEquals('column = \'Gifts\\\'--\'', (string)$cond->field('column')->equal('Gifts\'--'));
        $cond->clear();

        $this->assertEquals("column = 'Gifts\'+OR+1=1--'", (string)$cond->field('column')->equal('Gifts\'+OR+1=1--'));
        $cond->clear();

        $this->assertEquals("column >= 'Gifts\'+OR+1=1--'", (string)$cond->field('column')->largerOrEqualThan('Gifts\'+OR+1=1--'));
        $cond->clear();

        $this->assertEquals("column >= 'Gifts\'+OR+1=1--'", (string)$cond->field('column')->largerOrEqualThan('Gifts\'+OR+1=1--'));
        $cond->clear();
    }

    /**
     * @throws MySQLSyntaxException
     */
    public function testParenthesis()
    {
        $cond = new Conditions();

        $this->assertEquals('( column = \'value\' )', (string)$cond->
        openParenthesis()->field('column')->equal('value')->closeParenthesis());
        $cond->clear();

        $this->assertEquals('( column = \'value\' ) AND id = 2', (string)$cond->
        openParenthesis()->field('column')->equal('value')->closeParenthesis()->and()->field('id')->equal(2));
        $cond->clear();
    }

    /**
     * @throws MySQLSyntaxException
     * @throws QueryBuilderException
     * @throws QueryPathException
     */
    public function testSubQueries()
    {
        $qb = new QueryBuilder();

        $qb->insertInto('Persons', array(
            'PersonID' => '12345',
            'LastName' => 'Conditions',
            'FirstName' => 'Test',
            'Address' => 'UnitTest',
            'City' => 'PHP'
        ))->request();

        $qb->insertInto('Persons', array(
            'PersonID' => '123456',
            'LastName' => 'Conditions6',
            'FirstName' => 'Test6',
            'Address' => 'UnitTest6',
            'City' => 'PHP6'
        ))->request();

        $cond = $qb->createCondition();
        $response = $qb->select('*')->from('Persons')->where(
            $cond->field('PersonID')->in()->subQuery(
                $cond->createSubQuery()->select('PersonID')->from('Persons')->where(
                    $qb->createCondition()->field('PersonID')->equal('12345')->or()->field('PersonID')->equal('123456')
                )
            )
        )->request();

        $this->assertEquals(2, $response->getRowsNumber());
    }
}