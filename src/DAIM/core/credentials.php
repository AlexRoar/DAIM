<?php
/**
 * Copyright (c) 2020.
 * Designed and developed by Aleksandr Dremov
 * Use according to the license guidelines.
 * Contact me: dremov.me@gmail.com
 */

namespace DAIM\Core;

use DAIM\Exceptions\CredentialsException;

/**
 * Class Credentials
 * @package DAIM\Core
 */
abstract class Credentials
{

    static private $storage = array(
        'default' =>
            array(
                'host' => null,
                'username' => null,
                'password' => null,
                'dbname' => null,
                'port' => null,
                'socket' => null
            )
    );

    /**
     * @return string
     */
    public static function getHost($mode = 'default'): string
    {
        self::checkAndThrowModeError($mode);
        return self::$storage[$mode]['host'];
    }

    /**
     * @param string $host
     */
    public static function setHost($host, $mode = 'default'): void
    {
        self::checkAndThrowModeError($mode);
        self::$storage[$mode]['host'] = $host;
    }

    /**
     * @return string
     */
    public static function getUsername($mode = 'default'): string
    {
        self::checkAndThrowModeError($mode);
        return self::$storage[$mode]['username'];
    }

    /**
     * @param string $username
     */
    public static function setUsername($username, $mode = 'default'): void
    {
        self::checkAndThrowModeError($mode);
        self::$storage[$mode]['username'] = $username;
    }

    /**
     * @return string
     */
    public static function getPasswd($mode = 'default')
    {
        self::checkAndThrowModeError($mode);
        return self::$storage[$mode]['password'];
    }

    /**
     * @param string $passwd
     */
    public static function setPasswd($passwd, $mode = 'default'): void
    {
        self::checkAndThrowModeError($mode);
        self::$storage[$mode]['password'] = $passwd;
    }

    /**
     * @return string
     */
    public static function getDbname($mode = 'default'): string
    {
        self::checkAndThrowModeError($mode);
        return self::$storage[$mode]['dbname'];
    }

    /**
     * @param string $dbname
     */
    public static function setDbname($dbname, $mode = 'default'): void
    {
        self::checkAndThrowModeError($mode);
        self::$storage[$mode]['dbname'] = $dbname;
    }

    /**
     * @return int
     */
    public static function getPort($mode = 'default')
    {
        self::checkAndThrowModeError($mode);
        return self::$storage[$mode]['port'];
    }

    /**
     * @param int $port
     */
    public static function setPort($port, $mode = 'default'): void
    {
        self::checkAndThrowModeError($mode);
        self::$storage[$mode]['port'] = $port;
    }

    /**
     * @return string
     */
    public static function getSocket($mode = 'default')
    {
        self::checkAndThrowModeError($mode);
        return self::$storage[$mode]['socket'];
    }

    /**
     * @param string $socket
     */
    public static function setSocket($socket, $mode = 'default'): void
    {
        self::checkAndThrowModeError($mode);
        self::$storage[$mode]['socket'] = $socket;
    }

    /**
     *
     */
    public static function clear($mode = 'default'): void
    {
        self::checkAndThrowModeError($mode);
        self::$storage[$mode] = array(
            'host' => null,
            'username' => null,
            'password' => null,
            'dbname' => null,
            'port' => null,
            'socket' => null
        );
    }

    private static function isModeExists($mode)
    {
        return isset(self::$storage[$mode]);
    }

    private static function checkAndThrowModeError($mode)
    {
        if (!self::isModeExists($mode))
            throw new CredentialsException("Mode is not defined");
    }

    public static function createMode($mode)
    {
        if (isset(self::$storage[$mode]))
            throw new CredentialsException("Mode is already created");
        self::$storage[$mode] = array(
            'host' => null,
            'username' => null,
            'password' => null,
            'dbname' => null,
            'port' => null,
            'socket' => null);
    }

}
