<?php
declare(strict_types=1);

namespace concise;

use Closure;
use ReflectionMethod;
use ReflectionClass;
use ReflectionFunction;
use ArrayAccess;
use Countable;

class Container implements ArrayAccess,Countable {
    protected static $instance;     //容器自身对象

    protected $instances = [];      //容器实例对象

    protected $bind = [             //容器绑定标识
        'app'   => App::class,
        'config'=> Config::class,
        'http'  => Http::class,
        'route' => Route::class,
        'exception' => Exception::class,
        'env'   => Env::class,
    ];

    protected $alias = [];          //标识别名


    private function __construct(){}

    /**
     * 获取容器实例对象  （单例）
     * @return Container
     **/
    public static function getInstance() : Container {
        if (!isset(static::$instance)){
            static::$instance = new static;
        }
        return static::$instance;
    }

    public static function setInstance($instance) : void {
        static::$instance = $instance;
    }

    /**
     * 绑定一个类、闭包 实现到容器
     * @param $abstract string  类名 、别名
     * @param $value string|Closure   要绑定的类、闭包函数
     * @return boolean
     */
    public function bind($abstract,$value) {
        if (isset($this->bind[$abstract])){
            return $this->bind[$abstract];
        }
        $this->bind[$abstract] = $value;
    }

    /**
     * 创建一个类的实例
     * @param string    $abstract
     * @param array     $arg
     * @param bool      $newInstance
     */
    public function make(string $abstract,$arg = [],bool $newInstance = false) {
        $abstract = $this->alias[$abstract] ?? $abstract;
        if (isset($this->instances[$abstract]) && !$newInstance){       //判断容器里有没有这个实例 有则返回
            return $this->instances[$abstract];
        }
        if (isset($this->bind[$abstract])){
            $concrete = $this->bind[$abstract];

            if ($concrete instanceof Closure){
                //执行函数返回对象
               $object = $this->invokeFunc($concrete,$arg);
            }else{
                $this->alias[$abstract] = $concrete;
                return $this->make($concrete,$arg,$newInstance);
            }
        }else{
            $object = $this->invokeClass($abstract,$arg);
        }
        if (!isset($this->instances[$abstract])){
            $this->instances[$abstract] = $object;
        }
        return $object;
    }

    /**
     * 执行闭包
     * @param $func Closure    闭包函数
     * @param $arg  array      所需参数
     * @return
     */
    public function invokeFunc(callable $func,array $arg = []) {
        $reflect = new ReflectionFunction($func);

        $params = $this->bindParams($reflect,$arg);

        return call_user_func_array($func,$params);
    }

    /**
     * 通过反射获取一个类的实例 支持依赖注入
     * @param $className    string
     * @param $arg          array
     */
    private function invokeClass(string $className,$args = []) {
        $reflect = new ReflectionClass($className);

        if ($reflect->hasMethod('__make')) {
            $method = new ReflectionMethod($className, '__make');

            if ($method->isPublic() && $method->isStatic()) {
                $args = $this->bindParams($method, $args);
                return $method->invokeArgs(null, $args);
            }
        }
        $constructor = $reflect->getConstructor();

        $params = $this->bindParams($constructor,$args);
  
        return $reflect->newInstanceArgs($params);
    }

    /**
     * 绑定反射对象参数 支持依赖注入
     * @param  $reflect     ReflectionMethod|ReflectionFunction                     反射对象
     * @param  $arg         array                                                   所需参数
     * @return array                                                                组合完成后的参数
     */
    protected function bindParams($reflect,array $arg = []) {
        if ($reflect->getNumberOfParameters() === 0){
            return [];
        }
        reset($arg);
        $params = $reflect->getParameters();
        $argResult = [];
        foreach ($params as $param){

            $name = $param->getName();
            $class = $param->getClass();
            
            if ($class){                //对象类型传参
                $argResult[] = $this->getObjectParam($class->getName());
            }elseif (!empty($arg)){
                $argResult[] = array_shift($arg);
            }elseif($param->isDefaultValueAvailable()){
                $argResult[] = $param->getDefaultValue();
            }else{
                //抛异常 提示参数不完整
                die('param not exists '.$name);
            }
        }

        return $argResult;
    }

    /**
     * 获取对象类型参数
     * @param   $className  string     类名
     */
    protected function getObjectParam(string $className) {
        return $this->make($className);
    }



    public function __get($name) {
        if(isset($this->bind[$name])){
            return $this->make($this->bind[$name]);
        }
    }

    private function __clone(){}

    public function offsetExists($offset) {
        return isset($this->bind[$offset]) ? true : false;
    }

    public function offsetGet($offset) {
        return $this->instances[$offset] :: null;
    }

    public function offsetSet($offset, $value) {
        $this->bind[$offset] = $value;
    }

    public function offsetUnset($offset) {
        unset($this->bind[$offset]);
    }

    public function count() {
        return count($this->bind);
    }
}