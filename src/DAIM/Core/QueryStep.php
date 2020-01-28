<?php
/**
 * Copyright (c) 2020.
 * Designed and developed by Aleksandr Dremov
 * Use according to the license guidelines.
 * Contact me: dremov.me@gmail.com
 */

namespace DAIM\Core;


/**
 * Class QueryStep
 * @package DAIM\DAIM\Core
 */
class QueryStep
{
    /**
     * @var null
     */
    private $identifier = null;

    /**
     * @var null
     */
    private $value = null;

    /**
     * QueryStep constructor.
     * @param $identifier
     * @param $value
     */
    public function __construct($identifier, $value)
    {
        $this->identifier = $identifier;
        $this->value = $value;
    }

    /**
     * @return null
     */
    public function getIdentifier()
    {
        return $this->identifier;
    }

    /**
     * @return null
     */
    public function getValue()
    {
        return $this->value;
    }
}