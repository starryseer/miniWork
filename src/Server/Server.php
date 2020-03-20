<?php

namespace Starryseer\Work\Server;

abstract class Server
{
    public $pid;
    public $app;
    public $socket;
    public $onConnect;
    public $onReceive;
    public $addr;

    public function __construct()
    {
        $this->pid = posix_getpid();
        $this->addr = isset(app('config')->get('config')['address'])?app('config')->get('config')['address']:"tcp://0.0.0.0:8000";
    }

    public function wait()
    {
        $status = 0;
        while(true)
        {
            pcntl_wait($status);
        }
    }

    public abstract function send($conn, $content);

    public abstract function receive();

    public abstract function accept();

    public abstract function start();


}