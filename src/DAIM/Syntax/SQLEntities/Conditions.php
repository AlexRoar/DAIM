<?php
/**
 * Copyright (c) 2020.
 * Designed and developed by Aleksandr Dremov
 * Use according to the license guidelines.
 * Contact me: dremov.me@gmail.com
 */

namespace DAIM\Syntax\SQLEntities;


use DAIM\Core\Connection;
use DAIM\Exceptions\ConnectionException;
use DAIM\Exceptions\CredentialsException;
use DAIM\Exceptions\MySQLSyntaxException;
use DAIM\Exceptions\QueryBuilderException;
use Exception;

/**
 * Class Conditions
 * @package DAIM\Syntax\SQLEntities
 */
class Conditions implements BasicEntity
{

    /**
     * @var string
     */
    private $identifier = '{{conditions}}';

    /**
     * @var array
     */
    private $sequence = [];

    /**
     * @var
     */
    private $map;

    /**
     * @var
     */
    private $logicOperators;

    /**
     * @var string|null
     */
    private $mode = null;
    /**
     * @var
     */
    private $logicConjunctions;

    /**
     * Conditions constructor.
     * @param string $mode
     * @throws MySQLSyntaxException
     */
    public function __construct($mode = 'default')
    {
        $this->mode = $mode;
        try {
            $this->map = json_decode(file_get_contents(__DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'MySQL.json'), true);
            $this->logicOperators = $this->map['logicOperators'];
            $this->logicConjunctions = $this->map['logicConjunctions'];
        } catch (Exception $exception) {
            throw new MySQLSyntaxException("Unable to load MySQL schema: " . $exception->getMessage());
        }
    }

    /**
     * @return string
     */
    public function getMapName()
    {
        return $this->identifier;
    }

    /**
     * @param $field
     */
    public function field($field)
    {
        $this->sequence[] = $field;
        return $this;
    }

    /**
     *
     */
    public function equal()
    {
        $this->sequence[] = $this->logicOperators[__FUNCTION__];
        return $this;
    }

    /**
     *
     */
    public function largerThan()
    {
        $this->sequence[] = $this->logicOperators[__FUNCTION__];
        return $this;
    }

    /**
     *
     */
    public function lessThan()
    {
        $this->sequence[] = $this->logicOperators[__FUNCTION__];
        return $this;
    }

    /**
     *
     */
    public function largerOrEqualThan()
    {
        $this->sequence[] = $this->logicOperators[__FUNCTION__];
        return $this;
    }

    /**
     *
     */
    public function lessOrEqualThan()
    {
        $this->sequence[] = $this->logicOperators[__FUNCTION__];
        return $this;
    }

    /**
     *
     */
    public function notEqual()
    {
        $this->sequence[] = $this->logicOperators[__FUNCTION__];
        return $this;
    }

    /**
     *
     */
    public function equalNull()
    {
        $this->sequence[] = $this->logicOperators[__FUNCTION__];
        return $this;
    }

    /**
     *
     */
    public function notEqualNull()
    {
        $this->sequence[] = $this->logicOperators[__FUNCTION__];
        return $this;
    }

    /**
     *
     */
    public function between()
    {
        $this->sequence[] = $this->logicOperators[__FUNCTION__];
        return $this;
    }

    /**
     *
     */
    public function like()
    {
        $this->sequence[] = $this->logicOperators[__FUNCTION__];
        return $this;
    }

    /**
     *
     */
    public function inList()
    {
        $this->sequence[] = $this->logicOperators[__FUNCTION__];
        return $this;
    }

    /**
     *
     */
    public function exists()
    {
        $this->sequence[] = $this->logicOperators[__FUNCTION__];
        return $this;
    }

    /**
     * @param $value
     * @param string $type
     * @throws QueryBuilderException
     * @throws ConnectionException
     * @throws CredentialsException
     */
    public function value($value, $type = 'auto')
    {
        if (!Connection::isInitiated($this->mode))
            throw new QueryBuilderException(
                'Can\'t use value() and all other functions that work with user' .
                ' input and can result in SQL-injections without open Connection.' .
                ' Set up your \DAIM\Core\Connection class before using QueryBuilder!');

        switch (gettype($value)) {
            case 'bool':
            case 'int':
                $this->sequence[] = $value;
                break;
            case 'array':
                $value = json_encode($value);
                $this->sequence[] = '\'' . $this->realEscapeString($value) . '\'';
                break;
            case 'string':
            default:
                $this->sequence[] = '\'' . $this->realEscapeString($value) . '\'';
                break;
        }
        return $this;
    }

    /**
     *
     */
    public function true()
    {
        $this->sequence[] = true;
        return $this;
    }

    /**
     *
     */
    public function false()
    {
        $this->sequence[] = false;
        return $this;
    }

    /**
     *
     */
    public function and()
    {
        $this->sequence[] = $this->logicConjunctions[__FUNCTION__];
        return $this;
    }

    public function or()
    {
        $this->sequence[] = $this->logicConjunctions[__FUNCTION__];
        return $this;
    }

    public function openParenthesis()
    {
        $this->sequence[] = '(';
        return $this;
    }

    public function endParenthesis()
    {
        $this->sequence[] = ')';
        return $this;
    }

    /**
     * @param $value
     * @return mixed
     * @throws ConnectionException
     * @throws CredentialsException
     */
    private function escapeString($value)
    {
        return Connection::escapeString($value);
    }

    /**
     * @param $value
     * @return mixed
     * @throws ConnectionException
     * @throws CredentialsException
     */
    private function realEscapeString($value)
    {
        return Connection::realEscapeString($value);
    }

    /**
     * @return string
     */
    private function generateSQLString()
    {
        $outCommand = '';
        foreach ($this->sequence as $value)
            $outCommand .= (string)$value . ' ';
        return $outCommand;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->generateSQLString();
    }
}