<?php

namespace concise;


class App extends Container{

    protected $basePath;

    protected $appPath;


    /**
     * 配置文件后缀
     * @var string
     */

    public $configExt = '.php';

    public function __construct(){
        
        $this->basePath = dirname(__DIR__) .DIRECTORY_SEPARATOR;

        $this->appPath = $this->basePath . 'application' . DIRECTORY_SEPARATOR;

        static::setInstance($this);

        $this->instances[App::class] = $this;
    }

    public function getBashPath(){
        return $this->basePath;
    }

    public function getAppPath(){
        return $this->appPath;
    }

    public function getConfigDirPath()
    {
        return $this->basePath . 'config' . DIRECTORY_SEPARATOR;
    }

    public function getConfigExt()
    {
        return $this->configExt;
    }

}