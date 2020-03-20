<?php

namespace Starryseer\Work\Container;

class Container{
    protected $bindings = [];
    protected $instances = [];
    protected static $instance = null;

    public static function getInstance()
    {
        if(is_null(static::$instance))
            static::$instance = new static;

        return static::$instance;
    }




    public function bind($abstract,$bind)
    {
        try{
            if($bind instanceof \Closure)
            {
                $this->bindings[$abstract] = $bind;
            }

            $this->bindings[$abstract] = function ($params) use ($bind)
            {
                if(empty($params))
                    return new $bind();
                else
                    return new $bind($params);
            };
        }
        catch (\Exception $e)
        {
            var_dump($e->getMessage());
        }


    }

    public function make($abstract,$params='')
    {
        if(!isset($this->bindings[$abstract]))
            throw new Exception('没有找到这个容器对象'.$abstract, 500);

        if($this->has($abstract))
        {
            return $this->instances[$abstract];
        }


        $this->instances[$abstract] = $this->bindings[$abstract]($params);

        return $this->instances[$abstract];
    }

    public function has($abstract)
    {
        if(isset($this->instances[$abstract]))
            return true;

        return false;
    }

    public function setInstance($instance)
    {
        self::$instance = $instance;
    }


}