<?php /**
 * Copyright (c) 2020.
 * Designed and developed by Aleksandr Dremov
 * Use according to the license guidelines.
 * Contact me: dremov.me@gmail.com
 */ /** @noinspection PhpUnhandledExceptionInspection */
require_once __DIR__ . '/../vendor/autoload.php';

use DAIM\Core\Connection;
use DAIM\Core\Credentials;
use DAIM\Exceptions\CredentialsException;
use PHPUnit\Framework\TestCase;

/**
 * Class ConnectionTest
 */
class ConnectionTest extends TestCase
{

    private $host = '127.0.0.1';

    private $username = 'test';

    private $Dbname = 'daim';

    private $password = 'qwerty123';

    private $port = 3306;

    /**
     *
     */
    public function testGetConnection()
    {
        $cred = new Credentials();
        $cred->setHost($this->host);
        $cred->setUsername($this->username);
        $cred->setDbname($this->Dbname);
        $cred->setPassword($this->password);
        $cred->setPort($this->port);
        Connection::setCredentials($cred);
        $this->assertTrue(Connection::getConnection() instanceof mysqli);
    }

    public function testConnectionCredentialsSet()
    {
        $cred = new Credentials();
        $cred->setHost($this->host);
        $cred->setUsername($this->username);
        $cred->setDbname($this->Dbname);
        $cred->setPassword($this->password);
        $cred->setPort($this->port);
        Connection::setCredentials($cred);
        $this->assertTrue(Connection::getCredentials() instanceof Credentials);
    }

    /**
     * @depends testGetConnection
     * @expectedException DAIM\Exceptions\ConnectionException
     */
    public function testGetConnectionWrongCredentials()
    {
        $cred = new Credentials();
        $cred->setHost($this->host);
        $cred->setUsername($this->username . '_wrong_suffix'); # wrong
        $cred->setDbname($this->Dbname);
        $cred->setPassword($this->password . '_wrong_suffix'); # wrong
        $cred->setPort($this->port);
        Connection::setCredentials($cred);
        DAIM\core\Connection::initConnection();
    }

    public function testIsConnectionInitiated()
    {
        $this->assertFalse(Connection::isInitiated());

        $cred = new Credentials();
        $cred->setHost($this->host);
        $cred->setUsername($this->username);
        $cred->setDbname($this->Dbname);
        $cred->setPassword($this->password);
        $cred->setPort($this->port);
        Connection::setCredentials($cred);

        $this->assertFalse(Connection::isInitiated());

        Connection::initConnection();
        $this->assertTrue(Connection::isInitiated());

        Connection::createNewMode('secondMode');
        Connection::setCredentials($cred, 'secondMode');
        Connection::initConnection('secondMode');

        Connection::closeConnection('default');

        $this->assertTrue(Connection::isInitiated('secondMode'));
        $this->assertFalse(Connection::isInitiated());
    }

    /**
     * @throws CredentialsException
     */
    protected function tearDown()
    {
        if (Connection::isInitiated()) {
            Connection::closeConnection();
            Connection::clear();
        }
    }
}
