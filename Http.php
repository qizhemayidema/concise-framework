<?php
declare(strict_types=1);

namespace concise;

class Http{

    public $app;

    public function __construct(App $app){
        $this->app = $app;
    }

    public function run(){
        //注册异常处理
        $this->app->exception->register();
        //解析配置项
        $this->app->config->processConfigFileToArray();
        //解析路由
        $this->app->route;

        //注册事件

        //加载公共函数
    }
}