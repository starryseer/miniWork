<?php


namespace Starryseer\Work\Task;


use Starryseer\Work\Server\Server;

class Task
{
    public $task = [];
    public $taskNum;
    public $msg_key;
    public $msg_queue;
    public $onTask = null;
    public $server;


    public function __construct(Server $server)
    {
        $this->msg_key = isset(app('config')->get('config')['msg_key'])?ftok(app('config')->get('config')['msg_key'], 'u'):ftok(__FILE__, 'u');
        //产生一个消息队列
        $this->msg_queue = msg_get_queue($this->msg_key);
        $this->server = $server;

    }

    public function forkTask()
    {
        if(!isset(app('config')->get('config')['task_num']) or app('config')->get('config')['task_num'] <=0)
            return;

        for($i=1;$i<=app('config')->get('config')['task_num'];$i++)
        {
            $pid = pcntl_fork();
            if($pid <0)
            {
                throw new Exception('fork fail');
            }
            elseif($pid>0)
            {
                $this->task[(int)$i] = $pid;
            }
            else
            {
                $this->receiveTask($i);
                exit();
            }
        }
    }

    public function receiveTask($i)
    {
        while(true)
        {
            msg_receive($this->msg_queue,1,$i,1024,$data);
            if(is_callable($this->onTask))
                ($this->onTask)($this,$data);
        }
    }

    public function task($data,$task_id)
    {
        msg_send($this->msg_queue,$task_id,$data);
    }

    public function onTask(Closure $closure)
    {
        $this->onTask = $closure;
    }
}