<?php
declare (strict_types = 1);

namespace concise;

class Config{
    
    /**
     * 配置数组
     * @var array
     */
    protected $configSet = [];

    /**
     * 配置文件目录
     * @var array
     */
    protected $path;

    protected $ext;

    public function __construct(string $path,string $ext = '.php'){
        $this->path = $path;
        $this->ext = $ext;
    }

    public static function __make(App $app){

        $path = $app->getConfigDirPath();
        $ext = $app->getConfigExt();

        return new static($path,$ext);
    }

    /**
     * 解析配置文件
     */
    public function processConfigFileToArray(){
        $fileList = glob($this->path.'*'.$this->ext);

        foreach($fileList as $key => $value){
           $baseFileName = basename($value);
           $fileName = explode('.',$baseFileName)[0];
           $this->configSet[$fileName] = require_once $value;
        }
    }

    /**
     * 获取一组配置
     * @param string $name
     * @return mixed
     */
    public function getConfig(string $name)
    {
        return $this->configSet[$name];
    }
}