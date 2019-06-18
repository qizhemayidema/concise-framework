<?php
/**
 * Created by PhpStorm.
 * User: 刘彪
 * Date: 2019/5/29
 * Time: 8:49
 */
declare (strict_types = 1);


namespace concise;

use Closure;


/**
 * 解析所有路由 然后会通过配置项 来决定是否缓存
 * Class Route
 * @package concise
 */
class Route
{
    /**
     * 请求url
     * @var string
     */
    public $requestUrl = null;

    /**
     * 路由文件夹地址
     * @var string
     */
    public $routeDirPath ;

    public $config;

    /**
     * 路由集合
     * @var array
     *
     * [
     *  'article/{?}'     => [
     *          ['config']         => [],
     *          ['paramName']      => [],
     *          'handler'    => 'home\testController@index'
     *      ];
     * ]
     */
    public $routeSet = [];

    private $groupPrefix = '';

    private $groupStart = false;


    public function __construct(App $app)
    {
        $this->requestUrl = $_SERVER['REQUEST_URI'];

        $this->routeDirPath = $app->getRouteDirPath();

        $this->config = $app->config->getConfig('route');

        $this->load();
    }


    /**
     * get请求类型
     * @param $name string|array
     * @param $route string
     */
    public function get($name,$route){
        $this->addRoute('get',$name,$route);
    }

    /**
     * post请求类型
     * @param $name string|array
     * @param $route string
     */
    public function post($name,$route){
        $this->addRoute('post',$name,$route);
    }

    /**
     * put请求类型
     * @param $name string|array
     * @param $route string
     */
    public function put($name,$route){
        $this->addRoute('put',$name,$route);
    }

    /**
     * patch请求类型
     * @param $name string|array
     * @param $route string
     */
    public function patch($name,$route){
        $this->addRoute('patch',$name,$route);
    }

    /**
     * delete请求类型
     * @param $name string|array
     * @param $route string
     */
    public function delete($name,$route){
        $this->addRoute('delete',$name,$route);
    }

    /**
     * options请求类型
     * @param $name string|array
     * @param $route string
     */
    public function options($name,$route){
        $this->addRoute('options',$name,$route);
    }

    public function group($name,Closure $closure){

        $this->groupStart = false;
        if ($this->groupPrefix === ''){
            $this->groupStart = true;
        }

        $value = [];
        if (is_array($name)){
            $value['config'] = $name;
        }else{
            $value['config'] = ['prefix'=>$name];
        }
        $this->groupPrefix .= $value['config']['prefix'];

        Container::getInstance()->invokeFunc($closure);

        $this->groupStart = false;
        $this->groupPrefix = '';
    }


    /**
     * 还得改 解析完所有的路由 需要判断是否缓存
     */
    private function load()
    {
        $routeFileList = glob($this->routeDirPath.'*.php');

        foreach ($routeFileList as $key => $value ){
            require_once $value;
        }
    }

    /**
     * @param string $type
     * @param string|array $name
     * @param string|Closure $route
     */
    private function addRoute(string $type,$name,$controller)
    {
        $route = $this->groupPrefix . $name;

        $this->routeSet[$type][$route] = $controller;
    }
}