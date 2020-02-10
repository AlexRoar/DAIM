<?php
/**
 * Copyright (c) 2020.
 * Designed and developed by Aleksandr Dremov
 * Use according to the license guidelines.
 * Contact me: dremov.me@gmail.com
 */

namespace DAIM\Core;

/**
 * Class Credentials
 * @package DAIM\Core
 */
class Credentials
{

    /**
     * @var array
     */
    private $storage = array(
        'host' => null,
        'username' => null,
        'password' => null,
        'dbname' => null,
        'port' => null,
        'socket' => null
    );

    /**
     * @return string
     */
    public function getHost(): string
    {
        return $this->storage['host'];
    }

    /**
     * @param string $host
     */
    public function setHost($host): void
    {
        $this->storage['host'] = $host;
    }

    /**
     * @return string
     */
    public function getUsername(): string
    {
        return $this->storage['username'];
    }

    /**
     * @param string $username
     */
    public function setUsername($username): void
    {
        $this->storage['username'] = $username;
    }

    /**
     * @return string
     */
    public function getPassword()
    {
        return $this->storage['password'];
    }

    /**
     * @param $password
     */
    public function setPassword($password): void
    {
        $this->storage['password'] = $password;
    }

    /**
     * @return string
     */
    public function getDBname(): string
    {
        return $this->storage['dbname'];
    }

    /**
     * @param string $dbname
     */
    public function setDBname($dbname): void
    {
        $this->storage['dbname'] = $dbname;
    }

    /**
     * @return int
     */
    public function getPort()
    {
        return $this->storage['port'];
    }

    /**
     * @param int $port
     */
    public function setPort($port): void
    {
        $this->storage['port'] = $port;
    }

    /**
     * @return string
     */
    public function getSocket()
    {
        return $this->storage['socket'];
    }

    /**
     * @param string $socket
     */
    public function setSocket($socket): void
    {
        $this->storage['socket'] = $socket;
    }

    /**
     *
     */
    public function clear(): void
    {
        $this->storage = array(
            'host' => null,
            'username' => null,
            'password' => null,
            'dbname' => null,
            'port' => null,
            'socket' => null
        );
    }
}
