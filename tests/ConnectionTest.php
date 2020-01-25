<?php
/**
 * Copyright (c) 2020.
 * Designed and developed by Aleksandr Dremov
 * Use according to the license guidelines.
 * Contact me: dremov.me@gmail.com
 */

require_once __DIR__ . '/../vendor/autoload.php';

use DAIM\core\Connection;
use DAIM\Core\Credentials;
use DAIM\exceptions\ConnectionException;
use DAIM\Exceptions\CredentialsException;
use PHPUnit\Framework\TestCase;

/**
 * Class ConnectionTest
 */
class ConnectionTest extends TestCase
{
    /**
     *
     */
    public function testGetConnection()
    {
        Credentials::setHost('127.0.0.1');
        Credentials::setUsername('root');
        Credentials::setDbname('daim');
        Credentials::setPasswd('qwerty123');
        Credentials::setPort(8889);
        Connection::initConnection();
        $this->assertTrue(Connection::getConnection() instanceof mysqli);
    }

    /**
     * @depends testGetConnection
     * @expectedException ConnectionException
     */
    public function testGetConnectionWrongCredentials()
    {
        Credentials::setHost('127.0.0.1');
        Credentials::setUsername('root2'); # Wrong Username
        Credentials::setDbname('daim');
        Credentials::setPasswd('qwerty1234'); # Wrong password
        Credentials::setPort(8889);
        DAIM\core\Connection::initConnection();
    }

    /**
     * @depends testGetConnection
     */
    public function testSecondDatabaseConnection()
    {
        $secondDBMode = 'second';
        Credentials::createMode($secondDBMode);
        Credentials::setHost('127.0.0.1', $secondDBMode);
        Credentials::setUsername('root', $secondDBMode);
        Credentials::setDbname('daim', $secondDBMode);
        Credentials::setPasswd('qwerty123', $secondDBMode);
        Credentials::setPort(8889, $secondDBMode);
        Connection::initConnection($secondDBMode);
        $this->assertTrue(Connection::getConnection($secondDBMode) instanceof mysqli);
    }

    /**
     * @throws CredentialsException
     */
    protected function tearDown()
    {
        if (Connection::isInitiated())
            Connection::closeConnection();
        Connection::clear();
        Credentials::clear();
    }
}
