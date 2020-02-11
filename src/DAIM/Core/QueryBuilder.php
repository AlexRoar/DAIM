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
use DAIM\Syntax\SQLEntities\CloseParenthesis;
use DAIM\Syntax\SQLEntities\ColumnNames;
use DAIM\Syntax\SQLEntities\Conditions;
use DAIM\Syntax\SQLEntities\OpenParenthesis;
use DAIM\Syntax\SQLEntities\TableName;
use DAIM\Syntax\SQLEntities\TableNameGroup;
use DAIM\Syntax\SQLEntities\ValuesGroup;

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
                    $range = new ColumnNames([$range[0]]);
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
     * @return $this
     * @throws QueryPathException
     */
    public function insertInto($table = null, $values = null)
    {
        $this->makeStep('insert');
        $this->makeStep('into');
        if (!is_null($table)) {
            $this->tableName($table);
        }
        if (!is_null($values) and is_array($values)) {
            $this->columns(array_keys($values));
            $this->values(array_values($values));
        }
        return $this;
    }

    /**
     * @return $this
     * @throws QueryPathException
     */
    public function values(...$values)
    {
        $this->makeStep('values');
        if (count($values) != 0) {
            if (count($values) == 1 and is_array($values)) {
                $nextStep = new ValuesGroup($values[0]);
            } else {
                $nextStep = new ValuesGroup($values);
            }
            $this->makeStep($nextStep->getMapName(), $nextStep);
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

    /**
     * @param $name
     * @param mixed ...$names
     * @return $this
     * @throws QueryPathException
     */
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

    /**
     * @param mixed ...$names
     * @return $this
     * @throws QueryPathException
     */
    public function columns(...$names)
    {
        if (!is_array($names[0])) {
            $entitity = new ColumnNames($names);
            $this->makeStep($entitity->getMapName(), $entitity);
        } else {
            if (count($names) > 1)
                $entitity = new ColumnNames($names);
            else
                $entitity = new ColumnNames($names[0]);
            $this->makeStep($entitity->getMapName(), $entitity);
        }
        return $this;
    }

    /**
     * @param $step
     * @param string $value
     * @throws QueryPathException
     */
    private function makeStep($step, $value = '')
    {
        $this->autoStep();
        if (is_array($step)) {
            foreach ($step as $singleStep) {
                if ($this->isExpected($singleStep))
                    $step = $singleStep;
            }
            if (is_array($step)) {
                throw new QueryPathException('Unexpected sequence of query request. Requested: ' .
                    json_encode($step) .
                    '.These options were expected: ' . implode(", ", $this->expected));
            }
        }
        $step = trim($step);
        try {
            $this->checkIsExpectedAndThrowError($step);
        } catch (QueryPathException $exception) {
            foreach ($this->expected as $possibleStep) {
                if (in_array($possibleStep, ['{{open_parenthesis}}', '{{close_parenthesis}}'])) {
                    if ($possibleStep == '{{open_parenthesis}}')
                        $autoStep = new OpenParenthesis();
                    else
                        $autoStep = new CloseParenthesis();
                    $this->path->addPathStep($autoStep->getMapName(), $autoStep);
                    $this->updateExpectedValues();
                    $this->checkIsExpectedAndThrowError($step);
                    break;
                }
            }
        }
        $this->path->addPathStep($step, $value);
        $this->updateExpectedValues();
        $this->autoStep();
    }

    private function autoStep()
    {
        if (count($this->expected) == 1) {
            if (in_array($this->expected[0], ['{{open_parenthesis}}', '{{close_parenthesis}}'])) {
                if ($this->expected[0] == '{{open_parenthesis}}')
                    $autoStep = new OpenParenthesis();
                else
                    $autoStep = new CloseParenthesis();
                $this->path->addPathStep($autoStep->getMapName(), $autoStep);
                $this->updateExpectedValues();
            }
        }
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
        $sql = $this->generateQueryString();
        $mode = $this->mode;
        $this->clear();
        return new QueryResult($sql, $mode);
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
        $this->autoStep();
        return in_array(self::SEQUENCE_END_IDENTIFIER, $this->expected) or $this->expected == [];
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

    /**
     * @throws MySQLSyntaxException
     */
    public function clear()
    {
        $this->path = new QueryPath();
        $this->MySQL = new MySQL();
        $this->updateExpectedValues();
    }

    /**
     * @return Conditions
     * @throws MySQLSyntaxException
     */
    public function createCondition()
    {
        $cond = new Conditions($this->mode);
        return $cond;
    }


    public function delete()
    {
        $this->makeStep(__FUNCTION__);
        return $this;
    }

    public function __toString()
    {
        return $this->generateQueryString();
    }
}