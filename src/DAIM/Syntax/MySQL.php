<?php
/**
 * Copyright (c) 2020.
 * Designed and developed by Aleksandr Dremov
 * Use according to the license guidelines.
 * Contact me: dremov.me@gmail.com
 */

namespace DAIM\Syntax;


class MySQL
{
    public function __construct()
    {
        $data = json_decode(file_get_contents(get_class($this) . '.json'));
    }

    public function getExpected($path)
    {

    }
}