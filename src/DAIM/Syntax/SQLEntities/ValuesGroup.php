<?php
/**
 * Copyright (c) 2020.
 * Designed and developed by Aleksandr Dremov
 * Use according to the license guidelines.
 * Contact me: dremov.me@gmail.com
 */

namespace DAIM\Syntax\SQLEntities;


use DAIM\Core\Connection;

class ValuesGroup implements BasicEntity
{
    private $identifier = '{{values_group}}';

    private $storage = [];
    private $mode;

    public function __construct(array $storage = [], $mode = 'default')
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
        $outString = '(';

        $n = 0;
        foreach ($this->storage as $value) {
            $n++;
            $outString .= "'" . Connection::realEscapeString($value, $this->mode) . "'";
            if ($n != count($this->storage))
                $outString .= ', ';
        }
        $outString = trim($outString);
        $outString .= ')';

        return $outString;
    }
}