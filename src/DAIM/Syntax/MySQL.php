<?php
/**
 * Copyright (c) 2020.
 * Designed and developed by Aleksandr Dremov
 * Use according to the license guidelines.
 * Contact me: dremov.me@gmail.com
 */

namespace DAIM\Syntax;


use DAIM\Core\QueryPath;
use DAIM\Core\QueryStep;
use DAIM\Exceptions\MySQLSyntaxException;
use DAIM\Exceptions\QueryPathException;
use Exception;
use ReflectionClass;

/**
 * Class MySQL
 * @package DAIM\Syntax
 */
class MySQL
{

    /**
     * @var array|null
     */
    private $data = null;

    private const SEQUENCE_END_IDENTIFIER = "{{query_end}}";

    /**
     * MySQL constructor.
     * @throws MySQLSyntaxException
     */
    public function __construct()
    {
        try {
            $this->data = json_decode(file_get_contents(__DIR__ . DIRECTORY_SEPARATOR . (new ReflectionClass($this))->getShortName() . '.json'), true);
        } catch (Exception $exception) {
            throw new MySQLSyntaxException("Unable to load MySQL schema: " . $exception->getMessage());
        }
    }

    /**
     * @param QueryPath|null $path
     * @return array
     */
    public function getExpected(QueryPath $path = null): array
    {
        if ($path == null) {
            return array_keys($this->data['map']);
        } else if ($path->getPathLength() == 0) {
            return array_keys($this->data['map']);
        } else {
            return $this->iteratePathAndGetNextSteps($path);
        }
    }

    private function iteratePathAndGetNextSteps(QueryPath $path): array
    {
        $pathPoint = $this->data['map'];
        $currentPoint = [];
        foreach ($path as $key => $step) {
            /**
             * @var $step QueryStep
             */

            if (!isset($pathPoint[$step->getIdentifier()]))
                throw new QueryPathException("Unregistered path sequence: " . $step->getIdentifier());
            $pathPoint = $pathPoint[$step->getIdentifier()];
            $currentPoint[] = $step->getIdentifier();
            if (is_string($pathPoint)) {
                if ($pathPoint == self::SEQUENCE_END_IDENTIFIER) {
                    return [];
                } else {
                    $pathPoint = $this->followMapPseudoPath($pathPoint, $currentPoint);
                }
            }
        }
        return array_keys($pathPoint);
    }

    private function followMapPseudoPath(string $path, array $currentPoint)
    {
        if ($path[0] == '/')
            $currentPoint = [];
        $path = explode('/', $path);
        while (count($path) != 0) {
            switch ($path[0]) {
                case '..':
                    array_pop($currentPoint);
                    break;
                case '':
                    break;
                default:
                    $currentPoint[] = $path[0];
            }
            $path = array_reverse($path);
            array_pop($path);
            $path = array_reverse($path);
        }
        $mapPoint = $this->data['map'];
        foreach ($currentPoint as $way)
            $mapPoint = $mapPoint[$way];
        return $mapPoint;
    }
}