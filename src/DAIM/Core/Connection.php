<?php
/**
 * Copyright (c) 2020.
 * Designed and developed by Aleksandr Dremov
 * Use according to the license guidelines.
 * Contact me: dremov.me@gmail.com
 */

namespace DAIM\Core;

use DAIM\Exceptions\ConnectionException;
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
     * @var array
     */
    private static $connections = array(
        'default' => array(
            'connection' => null,
            'credentials' => null
        )
    );

    /**
     *
     * @param string $mode
     * @return mysqli
     * @throws ConnectionException
     */
    public static function getConnection($mode = 'default'): mysqli
    {
        $connection = self::$connections[$mode]['connection'];
        if (is_null($connection)) {
            self::initConnection($mode);
            $connection = self::$connections[$mode]['connection'];
        }
        if (!$connection instanceof mysqli) {
            throw new ConnectionException("Connection object is not instance of mysqli class :" . get_class($connection));
        } else {
            if ($connection->connect_errno) {
                throw new ConnectionException("Connection to DB failed " . $connection->connect_error);
            }
            if (!$connection->ping()) {
                throw new ConnectionException("Server is not alive " . $connection->error);
            }
        }
        return $connection;
    }

    /**
     * @param string $mode
     * @throws ConnectionException
     */
    public static function initConnection($mode = 'default'): void
    {
        try {
            self::$connections[$mode]['connection'] = new mysqli(self::$connections[$mode]['credentials']->getHost($mode),
                self::$connections[$mode]['credentials']->getUsername($mode),
                self::$connections[$mode]['credentials']->getPassword($mode),
                self::$connections[$mode]['credentials']->getDBname($mode),
                self::$connections[$mode]['credentials']->getPort($mode),
                self::$connections[$mode]['credentials']->getSocket($mode));
        } catch (Exception $exception) {
            throw new ConnectionException($exception->getMessage());
        }
    }

    /**
     *
     * @param string $mode
     * @throws CredentialsException
     */
    public static function closeConnection($mode = 'default'): void
    {
        self::checkAndThrowModeError($mode);
        if (!is_null(self::$connections[$mode]['connection']))
            self::$connections[$mode]['connection']->close();
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
     * @param string $mode
     * @throws CredentialsException
     */
    public static function clear($mode = 'default')
    {
        self::checkAndThrowModeError($mode);
        self::$connections[$mode] = array(
            'connection' => null,
            'credentials' => null
        );
    }

    /**
     * @param $mode
     * @throws CredentialsException
     */
    public static function createNewMode($mode)
    {
        if (self::isModeExists($mode))
            throw new CredentialsException("Mode already defined");
        else {
            self::$connections[$mode] = array(
                'connection' => null,
                'credentials' => null
            );
        }
    }

    /**
     * @param Credentials $credentials
     * @param string $mode
     * @throws CredentialsException
     */
    public static function setCredentials(Credentials $credentials, $mode = 'default')
    {
        self::checkAndThrowModeError();
        self::$connections[$mode]['credentials'] = $credentials;
    }

    /**
     * @param string $mode
     * @return mixed
     * @throws CredentialsException
     */
    public static function getCredentials($mode = 'default')
    {
        self::checkAndThrowModeError();
        return self::$connections[$mode]['credentials'];
    }

    /**
     * @param string $mode
     * @return bool
     */
    public static function isInitiated($mode = 'default'): bool
    {
        if (!self::isModeExists($mode))
            return false;
        if (self::$connections[$mode]['connection'] == null)
            return false;
        if (!self::$connections[$mode]['connection'] instanceof mysqli or !self::$connections[$mode]['credentials'] instanceof Credentials) {
            return false;
        } else {
            if (self::$connections[$mode]['connection']->connect_errno) {
                return false;
            }
            try {
                if (!self::$connections[$mode]['connection']->ping()) {
                    return false;
                }
            } catch (Exception $exception) {
                return false;
            }
            return true;
        }
    }

    /**
     * @param $charset
     * @param string $mode
     * @param bool|null $success
     * @throws CredentialsException
     */
    public static function setCharset($charset, $mode = 'default', bool &$success = null)
    {
        self::checkAndThrowModeError();
        $success = self::$connections[$mode]['connection']->set_charset($charset);
    }

    /**
     * @param $string
     * @param string $mode
     * @return mixed
     * @throws ConnectionException
     * @throws CredentialsException
     */
    public static function realEscapeString($string, $mode = 'default')
    {
        self::checkAndThrowModeError();
        if (!self::isInitiated($mode))
            throw new ConnectionException(
                'Can\'t use escapeString() without open Connection.' .
                ' Set up or init your ' . $mode . ' connection \DAIM\Core\Connection class before using QueryBuilder!');
        return self::$connections[$mode]['connection']->real_escape_string($string);
    }

    /**
     * @param $string
     * @param string $mode
     * @return mixed
     * @throws ConnectionException
     * @throws CredentialsException
     */
    public static function escapeString($string, $mode = 'default')
    {
        self::checkAndThrowModeError();
        if (!self::isInitiated($mode))
            throw new ConnectionException(
                'Can\'t use escapeString() without open Connection.' .
                ' Set up or init your ' . $mode . ' connection \DAIM\Core\Connection class before using QueryBuilder!');
        /**
         * @var $connections [$mode] mysqli
         */
        return self::$connections[$mode]['connection']->escape_string($string);
    }

    public static function query($sql, $mode = 'default')
    {
        return self::$connections[$mode]['connection']->query($sql);
    }

    public static function multiQuery($sql, $mode = 'default')
    {
        $finalRes = [];
        self::$connections[$mode]['connection']->multi_query($sql);
        do {
            if ($res = self::$connections[$mode]['connection']->store_result()) {
                $finalRes[] = $res;
                $res->free();
            }
        } while (self::$connections[$mode]['connection']->more_results() && self::$connections[$mode]['connection']->next_result());
        return $finalRes;
    }
}