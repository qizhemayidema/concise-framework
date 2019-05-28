<?php
/**
 * Created by PhpStorm.
 * User: 刘彪
 * Date: 2019/5/28
 * Time: 20:26
 */

namespace concise\facade;

use concise\Facade;

class Env extends Facade
{
    protected static function getFacadeClassName()
    {
        return 'env';
    }
}