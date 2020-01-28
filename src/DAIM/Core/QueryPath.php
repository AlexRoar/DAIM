<?php
/**
 * Copyright (c) 2020.
 * Designed and developed by Aleksandr Dremov
 * Use according to the license guidelines.
 * Contact me: dremov.me@gmail.com
 */

namespace DAIM\Core;


use DAIM\Exceptions\QueryPathException;
use Iterator;

/**
 * Class QueryPath
 * @package DAIM\DAIM\Core
 */
class QueryPath implements Iterator
{
    /**
     * @var array
     */
    private $path = [];

    /**
     * @var int
     */
    private $position = 0;

    /**
     * @param $identifier
     * @param $value
     */
    public function addPathStep($identifier, $value): void
    {
        $this->path[] = new QueryStep($identifier, $value);
    }

    /**
     * @return QueryStep
     * @throws QueryPathException
     */
    public function popPathStep(): QueryStep
    {
        if ($this->getPathLength() == 0) {
            throw new QueryPathException("Path is empty!");
        }
        return array_pop($this->path);
    }

    /**
     * @return int
     */
    public function getPathLength(): int
    {
        return count($this->path);
    }

    /**
     * @return QueryStep
     * @throws QueryPathException
     */
    public function getLastElement(): QueryStep
    {
        $len = $this->getPathLength();
        if ($len == 0)
            throw new QueryPathException("Path is empty!");
        return $this->path[$len - 1];

    }

    /**
     * @inheritDoc
     */
    public function current()
    {
        return $this->path[$this->position];
    }

    /**
     * @inheritDoc
     */
    public function next()
    {
        ++$this->position;
    }

    /**
     * @inheritDoc
     */
    public function key()
    {
        return $this->position;
    }

    /**
     * @inheritDoc
     */
    public function valid()
    {
        return isset($this->path[$this->position]);
    }

    /**
     * @inheritDoc
     */
    public function rewind()
    {
        $this->position = 0;
    }
}