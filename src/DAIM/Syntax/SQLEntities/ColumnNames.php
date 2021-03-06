<?php
/**
 * Copyright (c) 2020.
 * Designed and developed by Aleksandr Dremov
 * Use according to the license guidelines.
 * Contact me: dremov.me@gmail.com
 */

namespace DAIM\Syntax\SQLEntities;


class ColumnNames implements BasicEntity
{

    private $mapName = '{{column_names}}';

    private $names;

    public function __construct($names)
    {
        $this->names = $names;
    }

    public function getMapName()
    {
        return $this->mapName;
    }

    public function __toString()
    {
        return implode(', ', $this->names);
    }
}