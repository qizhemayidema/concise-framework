<?php

namespace concise\facade;

use concise\Facade;


class Route extends Facade
{
    protected static function getFacadeClassName()
    {
        return 'route';
    }
}