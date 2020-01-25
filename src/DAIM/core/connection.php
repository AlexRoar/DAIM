<?php
/**
 * Copyright (c) 2020.
 * Designed and developed by Aleksandr Dremov
 * Use according to the license guidelines.
 * Contact me: dremov.me@gmail.com
 */

namespace DAIM\core;

use DAIM\exceptions\ConnectionException;
use DAIM\Exceptions\CredentialsException;
use Exception;
use mysqli;

/**
 * Class Connection
 * @package DAIM\core
 */
abstract class Connection
{
    /**
     * Contains connection object or null if not initialized
     * @var null|mysqli
     */
    private static $connections = array(
        'default' => null
    );

    /**
     *
     * @return mysqli
     * @throws ConnectionException
     * @throws CredentialsException
     */
    public static function getConnection($mode = 'default'): mysqli
    {
        $connection = self::$connections[$mode];
        if (is_null($connection))
            self::initConnection($mode);
        if (!$connection instanceof mysqli) {
            throw new ConnectionException("Connection object is not instance of mysqli class");
        } else {
            if ($connection->connect_errno) {
                throw new ConnectionException("Connection to DB failed", $connection->connect_error);
            }
            if (!$connection->ping()) {
                throw new ConnectionException("Server is not alive", $connection->error);
            }
        }
        return $connection;
    }

    /**
     * @throws ConnectionException
     */
    public static function initConnection($mode = 'default'): void
    {
        try {
            self::$connections[$mode] = new mysqli(Credentials::getHost($mode),
                Credentials::getUsername($mode),
                Credentials::getPasswd($mode),
                Credentials::getDbname($mode),
                Credentials::getPort($mode),
                Credentials::getSocket($mode));
        } catch (Exception $exception) {
            throw new ConnectionException($exception->getMessage());
        }
    }

    /**
     *
     * @throws CredentialsException
     */
    public static function closeConnection($mode = 'default'): void
    {
        self::checkAndThrowModeError($mode);
        if (!is_null(self::$connections[$mode]))
            self::$connections[$mode]->close();
    }

    /**
     * @param $mode
     * @throws CredentialsException
     */
    private static function checkAndThrowModeError($mode = 'default')
    {
        if (!self::isModeExists($mode))
            throw new CredentialsException("Mode is not defined");
    }

    /**
     * @param $mode
     * @return bool
     */
    public static function isModeExists($mode = 'default'): bool
    {
        return array_key_exists($mode, self::$connections);
    }

    /**
     *
     * @throws CredentialsException
     */
    public static function clear($mode = 'default')
    {
        self::checkAndThrowModeError($mode);
        self::$connections[$mode] = null;
    }

    /**
     *
     */
    public static function isInitiated($mode = 'default'): bool
    {
        if (!self::isModeExists($mode))
            return false;
        if (self::$connections[$mode] == null)
            return false;
        if (!self::$connections[$mode] instanceof mysqli) {
            return false;
        } else {
            if (!self::$connections[$mode]->connect_errno and self::$connections[$mode]->ping()) {
                return true;
            }
        }
        return false;
    }
}