<?php
/**
 * Created by PhpStorm.
 * User: EDZ
 * Date: 2020/3/20
 * Time: 15:03
 */

namespace Starryseer\Work\RedisPool;

use Swoole\Database\RedisConfig;
use Swoole\Runtime;

class RedisPool
{
    protected $pool;
    protected $maxSize;
    protected $size;
    protected $host;
    protected $port;

    public function __construct()
    {
        $this->size = 0;
        $this->maxSize = isset(app('config')->get('redis')['poolSize'])?app('config')->get('redis')['poolSize']:10;
        $this->host = isset(app('config')->get('redis')['host'])?app('config')->get('redis')['host']:'127.0.0.1';
        $this->port = isset(app('config')->get('redis')['port'])?app('config')->get('redis')['port']:6379;
    }

    public function init()
    {
            $this->pool = new \Swoole\Database\RedisPool((new RedisConfig)
                ->withHost($this->host)
                ->withPort($this->port)
                ->withAuth('')
                ->withDbIndex(0)
                ->withTimeout(1)
            );

    }

    public function getObj()
    {
        return $this->pool->get();
    }

    function put($redis)
    {
        $this->pool->put($redis);
    }

    function get()
    {
        return $this->pool->get();
    }

    public function invoke(callable $call,float $timeout = null)
    {
        $obj = $this->getObj();
        if($obj)
        {
            try{
                return call_user_func($call,$obj);
            }
            catch(\Throwable $throwable) {
                throw $throwable;
            }
            finally{
                $this->put($obj);
            }
        }
        else
        {
            throw new \Exception('pool is empty');
        }

    }
}