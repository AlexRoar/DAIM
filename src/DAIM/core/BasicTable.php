<?php
/**
 * Copyright (c) 2020.
 * Designed and developed by Aleksandr Dremov
 * Use according to the license guidelines.
 * Contact me: dremov.me@gmail.com
 */

namespace DAIM\Core;


use DAIM\Exceptions\BasicTableException;
use DAIM\Exceptions\TableObjectException;

class BasicTable
{
    protected $tableName = null;

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
        if (is_null($this->tableName)) {
            throw new BasicTableException("
            Can't construct new QueryBuilder on unspecified table in BasicTable class!
             Use BasicTable only for extensions.");
        }
        $queryObject->setMainTable($this->tableName);
        return $queryObject;
    }
}