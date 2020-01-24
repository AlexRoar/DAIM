<?php
/**
 * Copyright (c) 2020.
 * Designed and developed by Aleksandr Dremov
 * Use according to the license guidelines.
 * Contact me: dremov.me@gmail.com
 */

require_once __DIR__ . '/../../vendor/autoload.php';

use PHPUnit\Framework\TestCase;

class BasicTableTest extends TestCase
{
    public function testGeneralIncludeAutoload()
    {
        $app = new DAIM\Core\BasicTable();
        $this->assertIsObject($app);
    }

}