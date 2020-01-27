<?php
/**
 * Copyright (c) 2020.
 * Designed and developed by Aleksandr Dremov
 * Use according to the license guidelines.
 * Contact me: dremov.me@gmail.com
 */

namespace DAIM\Core;


use DAIM\Exceptions\TableObjectException;

class BasicTable
{
    public function __construct($mode = 'default')
    {
        if (!Connection::isModeExists($mode))
            throw new TableObjectException('Connection mode ' . $mode . ' is not defined!');
        if (!Connection::isInitiated($mode))
            throw new TableObjectException('Connection mode ' . $mode . ' is not initiated!');
    }

    public function startQuery()
    {
        $queryObject = new QueryBuilder();
        return $queryObject;
    }
}