<?php
/**
 * Copyright (c) 2020.
 * Designed and developed by Aleksandr Dremov
 * Use according to the license guidelines.
 * Contact me: dremov.me@gmail.com
 */

namespace DAIM\Core;


use DAIM\Exceptions\MySQLSyntaxException;
use DAIM\Exceptions\QueryBuilderException;
use DAIM\Exceptions\QueryPathException;
use DAIM\Syntax\MySQL;
use DAIM\syntax\SQLEntities\BasicEntity;
use DAIM\Syntax\SQLEntities\ColumnNames;
use DAIM\Syntax\SQLEntities\Conditions;
use DAIM\Syntax\SQLEntities\TableName;
use DAIM\Syntax\SQLEntities\TableNameGroup;

/**
 * Class QueryBuilder
 * @package DAIM\Core
 */
class QueryBuilder
{
    /**
     * @var null | QueryPath
     */
    private $path = null;

    /**
     * @var array
     */
    private $expected = [];

    /**
     *
     */
    private const SEQUENCE_END_IDENTIFIER = "{{query_end}}";

    /**
     * @var MySQL
     */
    private $MySQL = null;

    /**
     * @var string
     */
    private $mode;

    /**
     * QueryBuilder constructor.
     * @param string $mode
     * @throws MySQLSyntaxException
     */
    public function __construct($mode = 'default')
    {
        $this->mode = $mode;
        $this->path = new QueryPath();
        $this->MySQL = new MySQL();
        $this->updateExpectedValues();
        if (!Connection::isInitiated($mode))
            throw new QueryBuilderException('You can\'t use Query Builder without activated connection.' .
                ' Initiate your connection using Connection::setCredentials(<credentials>) ' .
                'and do not forget about Connection::initConnection()!');
    }


    /**
     * @param null $range
     * @return $this
     * @throws QueryPathException
     */
    public function select(...$range)
    {
        $this->makeStep(__FUNCTION__);
        if (count($range) != 0) {
            if (count($range) == 1) {
                if ($range[0] == '*' or strtolower($range[0]) == 'all')
                    $this->makeStep('*');
                else {
                    $range = new ColumnNames($range[0]);
                    $this->makeStep($range->getMapName(), $range);
                }
            } else {
                $range = new ColumnNames($range);
                $this->makeStep($range->getMapName(), $range);
            }
        }
        return $this;
    }

    /**
     * @return $this
     * @throws QueryPathException
     */
    public function all()
    {
        $this->makeStep('*');
        return $this;
    }

    /**
     * @return $this
     * @throws QueryPathException
     */
    public function star()
    {
        $this->makeStep('*');
        return $this;
    }

    /**
     * @param string|array|TableNameGroup|TableName $fromWhat
     * @return $this
     * @throws QueryPathException
     */
    public function from(...$fromWhat)
    {
        $this->makeStep(__FUNCTION__);
        if (count($fromWhat) != 0) {
            if (count($fromWhat) == 1) {
                if (is_string($fromWhat[0])) {
                    $fromWhat = new TableName($fromWhat[0]);
                    $this->makeStep($fromWhat->getMapName(), $fromWhat);
                } elseif ($fromWhat[0] instanceof TableName) {
                    $fromWhat = $fromWhat[0];
                    $this->makeStep($fromWhat->getMapName(), $fromWhat);
                }
            } else {
                $fromWhat = new TableNameGroup($fromWhat);
                $this->makeStep($fromWhat->getMapName(), $fromWhat);
            }
        }
        return $this;
    }

    /**
     * @return $this
     * @throws QueryPathException
     */
    public function where($condition = null)
    {
        $this->makeStep(__FUNCTION__);
        if (!is_null($condition)) {
            $this->conditions($condition);
        }
        return $this;
    }

    /**
     * @param string $name
     * @return QueryBuilder
     * @throws QueryPathException
     */
    public function tableName($name)
    {
        $value = new TableName($name);
        $this->makeStep($value->getMapName(), $value);
        return $this;
    }

    public function tableNames($name, ...$names)
    {
        $namesAll = array_merge([$name], $names);

        $value = new TableNameGroup($namesAll);
        $this->makeStep($value->getMapName(), $value);
        return $this;
    }

    /**
     * @param Conditions $conditions
     * @return $this
     * @throws QueryPathException
     */
    public function conditions(Conditions $conditions)
    {
        $this->makeStep($conditions->getMapName(), $conditions);
        return $this;
    }

    public function columns(...$names)
    {
        $entitity = new ColumnNames($names);
        $this->makeStep($entitity->getMapName(), $entitity);
        return $this;
    }

    /**
     * @param $step
     * @param string $value
     * @throws QueryPathException
     */
    private function makeStep($step, $value = '')
    {
        if (is_array($step)) {
            foreach ($step as $singleStep) {
                if ($this->isExpected($singleStep))
                    $step = $singleStep;
            }
        }
        $step = trim($step);
        $this->checkIsExpectedAndThrowError($step);
        $this->path->addPathStep($step, $value);
        $this->updateExpectedValues();
    }


    /**
     * @param $operation
     * @return bool
     */
    private function isExpected($operation)
    {
        return in_array($operation, $this->expected);
    }

    /**
     * @throws QueryBuilderException
     */
    public function request(): QueryResult
    {
        if (!$this->isSequenceCanBeEnded())
            throw new QueryBuilderException('Can\'t be requested at this moment. Expected further steps: ' . implode(' ', $this->expected));
        return new QueryResult($this->generateQueryString(), $this->mode);
    }

    /**
     * @return string
     */
    private function generateQueryString()
    {
        /**
         * @var $step QueryStep
         */
        $outCommand = '';
        foreach ($this->path as $step) {
            if ($step->getValue() instanceof BasicEntity)
                $outCommand .= (string)$step->getValue() . ' ';
            else
                $outCommand .= strtoupper($step->getIdentifier()) . ' ';
        }
        return trim($outCommand);
    }

    /**
     * @return bool
     */
    private function isSequenceCanBeEnded()
    {
        return in_array(self::SEQUENCE_END_IDENTIFIER, $this->expected);
    }

    /**
     * @param $operation
     * @throws QueryPathException
     */
    private function checkIsExpectedAndThrowError($operation)
    {
        if (!$this->isExpected($operation))
            throw new QueryPathException('Unexpected sequence of query request. Requested: ' .
                $operation .
                '.These options were expected: ' . implode(", ", $this->expected));
    }

    /**
     *
     */
    private function updateExpectedValues()
    {
        $this->expected = $this->MySQL->getExpected($this->path);
    }

    public function clear()
    {
        $this->path = new QueryPath();
        $this->MySQL = new MySQL();
        $this->updateExpectedValues();
    }
}