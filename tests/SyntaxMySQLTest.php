<?php
/**
 * Copyright (c) 2020.
 * Designed and developed by Aleksandr Dremov
 * Use according to the license guidelines.
 * Contact me: dremov.me@gmail.com
 */

use DAIM\Core\QueryPath;
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
            $method->invokeArgs($MySQL, ['../', ['insert', 'into', '{{table_name}}', '{{column_names}}']]),
            ['insert', 'into', '{{table_name}}']);
        $this->assertEquals(
            $method->invokeArgs($MySQL, ['..', ['insert', 'into', '{{table_name}}', '{{column_names}}']]),
            ['insert', 'into', '{{table_name}}']);
        $this->assertEquals(
            $method->invokeArgs($MySQL, ['../../values/{{values_group}}', ['insert', 'into', '{{table_name}}', '{{column_names}}', 'values']]),
            ['insert', 'into', '{{table_name}}', 'values', '{{values_group}}']);
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
        $this->assertTrue(true);
    }
}