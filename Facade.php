<?php
/**
 * Created by PhpStorm.
 * User: 刘彪
 * Date: 2019/5/28
 * Time: 20:28
 */

namespace concise;


abstract class Facade
{
    abstract protected static function getFacadeClassName();

    protected static function createFacade($args = [])
    {
        $className = static::getFacadeClassName();

        return Container::getInstance()->make($className,$args);
    }

    public static function __callStatic($method, $args)
    {
        return call_user_func_array([static::createFacade(),$method],$args);
    }
}