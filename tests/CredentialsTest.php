<?php
/**
 * Copyright (c) 2020.
 * Designed and developed by Aleksandr Dremov
 * Use according to the license guidelines.
 * Contact me: dremov.me@gmail.com
 */

use DAIM\Core\Credentials;
use PHPUnit\Framework\TestCase;

/**
 * Class CredentialsTest
 */
class CredentialsTest extends TestCase
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
    }

    /**
     */
    public function testBasicCredentialsSetup()
    {
        $cred = new Credentials();
        $cred->setHost('foo1');
        $cred->setUsername('foo2');
        $cred->setPassword('foo3');
        $cred->setSocket('foo4');
        $cred->setPort(1000);
        $cred->setDBname('foo5');

        $this->assertEquals($cred->getHost(), 'foo1');
        $this->assertEquals($cred->getUsername(), 'foo2');
        $this->assertEquals($cred->getPassword(), 'foo3');
        $this->assertEquals($cred->getSocket(), 'foo4');
        $this->assertEquals($cred->getPort(), 1000);
        $this->assertEquals($cred->getDBname(), 'foo5');
    }

    /**
     *
     */
    public function testSecondCredentialsSetup()
    {
        $cred = new Credentials();
        $cred->setHost('foo1');
        $cred->setUsername('foo2');
        $cred->setPassword('foo3');
        $cred->setSocket('foo4');
        $cred->setPort(1000);
        $cred->setDBname('foo5');

        $credSecond = new Credentials();
        $credSecond->setHost('foo12');
        $credSecond->setUsername('foo22');
        $credSecond->setPassword('foo32');
        $credSecond->setSocket('foo42');
        $credSecond->setPort(10002);
        $credSecond->setDBname('foo52');

        $this->assertEquals($cred->getHost(), 'foo1');
        $this->assertEquals($cred->getUsername(), 'foo2');
        $this->assertEquals($cred->getPassword(), 'foo3');
        $this->assertEquals($cred->getSocket(), 'foo4');
        $this->assertEquals($cred->getPort(), 1000);
        $this->assertEquals($cred->getDBname(), 'foo5');

        $this->assertEquals($credSecond->getHost(), 'foo12');
        $this->assertEquals($credSecond->getUsername(), 'foo22');
        $this->assertEquals($credSecond->getPassword(), 'foo32');
        $this->assertEquals($credSecond->getSocket(), 'foo42');
        $this->assertEquals($credSecond->getPort(), 10002);
        $this->assertEquals($credSecond->getDBname(), 'foo52');
    }
}