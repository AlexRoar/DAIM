<?php
/**
 * Copyright (c) 2020.
 * Designed and developed by Aleksandr Dremov
 * Use according to the license guidelines.
 * Contact me: dremov.me@gmail.com
 */

use DAIM\Core\QueryPath;
use DAIM\Exceptions\MySQLSyntaxException;
use DAIM\Syntax\MySQL;
use PHPUnit\Framework\TestCase;

/**
 * Copyright (c) 2020.
 * Designed and developed by Aleksandr Dremov
 * Use according to the license guidelines.
 * Contact me: dremov.me@gmail.com
 */
class SyntaxMySQLTest extends TestCase
{
    /**
     * @throws ReflectionException
     */
    public function testMapPathwayPseudoPath()
    {
        $method = new ReflectionMethod('DAIM\Syntax\MySQL', 'followMapPseudoPath');
        $method->setAccessible(true);

        $MySQL = new MySQL();
        $this->assertEquals(
            array_keys($method->invokeArgs($MySQL, ['../', ['insert', 'into', '{{table_name}}', '{{column_names}}']])),
            ['values', '{{column_names}}']);
        $this->assertEquals(
            array_keys($method->invokeArgs($MySQL, ['..', ['insert', 'into', '{{table_name}}', '{{column_names}}']])),
            ['values', '{{column_names}}']);
        $this->assertEquals(
            $method->invokeArgs($MySQL, ['../../values/{{values_group}}', ['insert', 'into', '{{table_name}}', '{{column_names}}', 'values']]),
            '{{query_end}}');
    }

    /**
     * @depends testMapPathwayPseudoPath
     * @throws ReflectionException
     */
    public function testNextStepsRetrieve()
    {
        $method = new ReflectionMethod('DAIM\Syntax\MySQL', 'iteratePathAndGetNextSteps');
        $method->setAccessible(true);

        $MySQL = new MySQL();

        $path = new QueryPath();
        $path->addPathStep('select', '');
        $path->addPathStep('*', '');
        $this->assertEquals(
            $method->invokeArgs($MySQL, [$path]), ['from']);

        $path->addPathStep('from', '');
        $path->addPathStep('{{table_name_group}}', '');
        $path->addPathStep('where', '');
        $this->assertEquals(
            $method->invokeArgs($MySQL, [$path]), ['{{conditions}}']);
    }

    /**
     * @expectedException  DAIM\Exceptions\QueryPathException
     * @depends testNextStepsRetrieve
     * @throws MySQLSyntaxException
     * @throws ReflectionException
     */
    public function testNextStepsRetrieveWithUnregisteredWay()
    {
        $method = new ReflectionMethod('DAIM\Syntax\MySQL', 'iteratePathAndGetNextSteps');
        $method->setAccessible(true);

        $MySQL = new MySQL();

        $path = new QueryPath();
        $path->addPathStep('select', '');
        $path->addPathStep('unregistered_WAY', '');
        $method->invokeArgs($MySQL, [$path]);
    }

    public function testComplicatedPathExpectedValues()
    {
        $method = new ReflectionMethod('DAIM\Syntax\MySQL', 'iteratePathAndGetNextSteps');
        $method->setAccessible(true);
        $MySQL = new MySQL();

        $path = new QueryPath();
        $path->addPathStep('insert', '');
        $path->addPathStep('into', '');
        $path->addPathStep('{{table_name}}', '');
        $path->addPathStep('{{column_names}}', '');
        $this->assertEquals(
            $method->invokeArgs($MySQL, [$path]),
            ['values', 'select']);

        $path->addPathStep('values', '');
        $this->assertEquals(
            $method->invokeArgs($MySQL, [$path]),
            ['{{values_group}}']);

        $path->popPathStep();
        $path->addPathStep('select', '');
        $this->assertEquals(
            $method->invokeArgs($MySQL, [$path]),
            ['*', 'all', '{{column_names}}', 'count', 'sum', 'avg', 'max', 'min']);
    }
}