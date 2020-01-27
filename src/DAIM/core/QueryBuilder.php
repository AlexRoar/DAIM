<?php
/**
 * Copyright (c) 2020.
 * Designed and developed by Aleksandr Dremov
 * Use according to the license guidelines.
 * Contact me: dremov.me@gmail.com
 */

namespace DAIM\Core;


class QueryBuilder
{
    private $path = [];

    private $expected = [];

    private $mainTable = null;

    public function __construct()
    {

    }

    public function setMainTable($tableName)
    {
        $this->mainTable = $tableName;
    }

}