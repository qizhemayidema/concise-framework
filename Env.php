<?php
/**
 * Created by PhpStorm.
 * User: 刘彪
 * Date: 2019/5/28
 * Time: 17:56
 */
declare (strict_types = 1);

namespace concise;


class Env
{
    protected $app;

    protected $name = '.env';

    protected $path = '';

    protected $envSet = [];

    public function __construct(App $app)
    {
        $this->app = $app;

        $this->path = $this->app->getBasePath() . $this->name;

        $this->processEnvFileToArray();
    }

    /**
     * 获取env文件配置项
     * @param string $name
     * @param string|null $defaultValue
     * @return string
     */
    public function get(string $name,string $defaultValue = null)
    {
        $nameList = explode('.',$name);

        $typeName = $nameList[0];

        $optionName = $nameList[1];

        if (isset($this->envSet[$typeName]) && isset($this->envSet[$typeName][$optionName])){
            return $this->envSet[$typeName][$optionName];
        }
        return $defaultValue;
    }


    private function processEnvFileToArray()
    {
        if (!file_exists($this->path)){
            return false;
        }
        $this->envSet = parse_ini_file($this->path,true);
    }


}