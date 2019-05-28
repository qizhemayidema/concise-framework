<?php
/**
 * Created by PhpStorm.
 * User: 刘彪
 * Date: 2019/5/28
 * Time: 17:40
 */

namespace concise;


class Exception
{
    public function __construct(){}

    public function register()
    {

        set_error_handler(function($errno, $errstr, $errfile, $errline){
            echo "<b>Custom error:</b> [$errno] $errstr<br>";
            echo " Error on line $errline in $errfile<br>";
        },E_ALL);

        set_exception_handler(function($e){
            echo "<b>Exception:</b> ", $e->getMessage();
        });
    }
}