<?php
/**
 * Copyright (c) 2020.
 * Designed and developed by Aleksandr Dremov
 * Use according to the license guidelines.
 * Contact me: dremov.me@gmail.com
 */

namespace DAIM\Syntax\SQLEntities;


/**
 * Class TableNames
 * @package DAIM\Syntax\SQLEntities
 */
class TableName implements BasicEntity
{
    /**
     * @var string
     */
    private $name = null;
    /**
     * @var string
     */
    private $identifier = ['{{table_name}}', '{{table_name_group}}'];

    /**
     * TableNames constructor.
     * @param $name
     */
    function __construct($name)
    {
        $this->name = $name;
    }

    /**
     *
     */
    public function getMapName()
    {
        return $this->identifier;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->name;
    }
}