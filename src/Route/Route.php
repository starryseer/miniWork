<?php


namespace Starryseer\Work\Route;


class Route
{
    private $routeMap;
    private $route = [];
    private $methods = ['GET','POST'];

    public function __construct()
    {
        $this->routeMap = [
            'Http'=>app()->getBasePath().'/route/http.php'
        ];
    }

    public function register()
    {
        foreach ($this->routeMap as $path)
        {
            require_once $path;
        }
    }

    public function addRoute($methods,$path,$ctrl)
    {
        foreach($methods as $method)
        {
            $this->route[$method][$path] = $ctrl;
        }
        return $this;
    }

    public function get($path,$ctrl)
    {
        if(strpos($ctrl,'@') === false and !($ctrl instanceof Closure))
            return;

        $this->addRoute(['GET'],$path,$ctrl);
    }

    public function post($path,$ctrl)
    {
        if(strpos($ctrl,'@') === false and !($ctrl instanceof Closure))
            return;

        $this->addRoute(['POST'],$path,$ctrl);
    }

    public function any($path,$ctrl)
    {
        if(strpos($ctrl,'@') === false and !($ctrl instanceof Closure))
            return;

        $this->addRoute($this->methods,$path,$ctrl);
    }

    public function match($request)
    {
        if(!isset($this->route[$request->getMethod()][$request->getPath()]))
            return false;

        $ctrl = $this->route[$request->getMethod()][$request->getPath()];
        if($ctrl instanceof Closure)
            return $ctrl;
        else
            return explode('@',$ctrl,2);
    }


}