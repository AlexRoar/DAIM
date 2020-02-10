<?php
/**
 * Copyright (c) 2020.
 * Designed and developed by Aleksandr Dremov
 * Use according to the license guidelines.
 * Contact me: dremov.me@gmail.com
 */

use DAIM\Core\Connection;
use DAIM\Core\Credentials;
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
}