<?php


namespace Starryseer\Work\Worker;

use Swoole\Coroutine;

class Worker
{
    public $worker = [];
    public $workerNum;
    public $onWorkStart;

    public function forkWorker()
    {
        $this->workerNum = isset(app('config')->get('config')['worker_num'])?app('config')->get('config')['worker_num']:1;

        for($i=0;$i<$this->workerNum ;$i++)
        {
            $pid = pcntl_fork();
            if($pid < 0)
                throw new Exception('进程创建失败');
            elseif($pid == 0)
            {
                Coroutine\run(function () {
                    app()->bind('redis', \Starryseer\Work\RedisPool\RedisPool::class);
                    app('redis');
                    app('redis')->init();
                    app('httpServer')->accept();
                });
                exit();
            }
            else
                $this->worker[] = $pid;
        }
    }

    public function wait()
    {
        $status = 0;
        while(true)
        {
            \pcntl_wait($status);
        }
    }
}