<?php
/**
 * Copyright (c) 2020.
 * Designed and developed by Aleksandr Dremov
 * Use according to the license guidelines.
 * Contact me: dremov.me@gmail.com
 */

namespace DAIM\Core;


abstract class Credentials
{
    static $type = 'mysql';

    static $host = null;

    static $username = null;

    static $passwd = null;

    static $dbname = null;

    static $port = null;

    static $socket = null;

}
