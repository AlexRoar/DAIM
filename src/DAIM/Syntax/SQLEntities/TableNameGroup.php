<?php
/**
 * Copyright (c) 2020.
 * Designed and developed by Aleksandr Dremov
 * Use according to the license guidelines.
 * Contact me: dremov.me@gmail.com
 */

namespace DAIM\Syntax\SQLEntities;


class TableNameGroup implements BasicEntity
{

    private $identifier = '{{table_name_group}}';

    private $names;

    public function __construct($names)
    {
        $this->names = $names;
    }

    public function getMapName()
    {
        return $this->identifier;
    }

    public function __toString()
    {
        return implode(', ', $this->names);
    }
}