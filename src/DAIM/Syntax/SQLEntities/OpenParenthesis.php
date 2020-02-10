<?php
/**
 * Copyright (c) 2020.
 * Designed and developed by Aleksandr Dremov
 * Use according to the license guidelines.
 * Contact me: dremov.me@gmail.com
 */

namespace DAIM\Syntax\SQLEntities;


class OpenParenthesis implements BasicEntity
{

    public function getMapName()
    {
        return '{{open_parenthesis}}';
    }

    public function __toString()
    {
        return '(';
    }
}