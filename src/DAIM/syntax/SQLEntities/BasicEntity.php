<?php
/**
 * Copyright (c) 2020.
 * Designed and developed by Aleksandr Dremov
 * Use according to the license guidelines.
 * Contact me: dremov.me@gmail.com
 */

namespace DAIM\syntax\SQLEntities;


interface BasicEntity
{
    public function toSQL();

    public function getMapName();
}