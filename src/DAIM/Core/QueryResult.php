<?php
/**
 * Copyright (c) 2020.
 * Designed and developed by Aleksandr Dremov
 * Use according to the license guidelines.
 * Contact me: dremov.me@gmail.com
 */

namespace DAIM\Core;


use mysqli_result;

/**
 * Class QueryResult
 * @package DAIM\Core
 */
class QueryResult
{

    /**
     * @var mysqli_result|null
     */
    private $result = null;

    /**
     * QueryResult constructor.
     * @param $sql
     * @param $mode
     */
    public function __construct($sql, $mode)
    {
        $this->result = Connection::query($sql, $mode);
    }

    /**
     *
     */
    public function free()
    {
        return $this->result->free();
    }

    public function fetchAll($resulttype = MYSQLI_NUM)
    {
        return $this->result->fetch_all($resulttype);
    }

    public function getRowsNumber()
    {
        return $this->result->num_rows;
    }

}