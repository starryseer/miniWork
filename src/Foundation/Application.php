<?php
/**
 * Created by PhpStorm.
 * User: EDZ
 * Date: 2020/3/11
 * Time: 16:24
 */

namespace Starryseer\Work\Foundation;


use Starryseer\Work\Container\Container;

class Application extends Container
{
    private $basePath;


    public function __construct($path=null)
    {
        if(!empty($path))
            $this->setBasePath($path);

        self::setInstance($this);
        $this->registerBindings();
        $this->init();
    }

    public function run()
    {
        try{
            $http = $this->make('httpServer');
            $http->start();
        }
        catch (Exception $e)
        {
            var_dump($e->getMessage());
        }

    }

    public function registerBindings()
    {
        $binds = [
            'config'=>\Starryseer\Work\Config\Config::class,
            'httpRequest'=>\Starryseer\Work\Request\Http\HttpRequest::class,
            'task'=>\Starryseer\Work\Task\Task::class,
            'worker'=>\Starryseer\Work\Worker\Worker::class,
            'server'=>\Starryseer\Work\Server\Server::class,
            'httpServer'=>\Starryseer\Work\Server\Http\HttpServer::class,
            'route'=>\Starryseer\Work\Route\Route::class,
        ];
        foreach($binds as $abstract => $bind)
        {
            $this->bind($abstract,$bind);
        }
    }

    public function init()
    {
        app('route')->register();
    }

    public function setBasePath($dir)
    {
        $this->basePath = \rtrim($dir,'\/');
    }

    public function getBasePath()
    {
        return $this->basePath;
    }
}