<?php
/**
 * Copyright (c) 2020.
 * Designed and developed by Aleksandr Dremov
 * Use according to the license guidelines.
 * Contact me: dremov.me@gmail.com
 */

namespace DAIM\Syntax\SQLEntities;


use DAIM\Core\Connection;

/**
 * Class ColumnValueGroups
 * @package DAIM\Syntax\SQLEntities
 */
class ColumnValueGroups implements BasicEntity
{
    private $storage = [];
    /**
     * @var string
     */
    private $mode;

    private $identifier = '{{column_value_groups}}';

    public function __construct($mode = 'default', array $storage = [])
    {
        $this->storage = $storage;
        $this->mode = $mode;
    }

    public function getMapName()
    {
        return $this->identifier;
    }

    public function __toString()
    {
        $outString = '';

        $n = 0;
        foreach ($this->storage as $column => $value) {
            $n++;
            $outString .= $column . "='" . Connection::realEscapeString($value, $this->mode) . "'";
            if ($n != count($this->storage))
                $outString .= ', ';
        }

        return trim($outString);
    }
}