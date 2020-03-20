<?php
namespace Starryseer\Work\Server\Http;

use Starryseer\Work\Foundation\Application;
use Starryseer\Work\Server\Server;
use Swoole\Event;

class HttpServer extends Server
{
    public function __construct()
    {
        Parent::__construct();
        $this->onReceive = function ($server,$socket,$data){
            $this->onReceiveBack($server,$socket,$data);
        };
    }

    public function accept()
    {
        $this->createSocket();
        Event::add($this->socket, $this->_accept());
    }

    public function createSocket()
    {
        try{
            $context = stream_context_create([
                'socket' => [
                    // 设置等待资源的个数
                    'backlog' => '102400',
                ],
            ]);
            // 设置端口可以重复监听
            \stream_context_set_option($context, 'socket', 'so_reuseport', 1);

            // 传递一个资源的文本 context
            return $this->socket = stream_socket_server($this->addr , $errno , $errstr, STREAM_SERVER_BIND | STREAM_SERVER_LISTEN, $context);

        }
        catch (\Exception $e)
        {
            echo $e->getMessage();
        }

    }

    public function _accept()
    {
        return function($socket) {
            $conn = @stream_socket_accept($this->socket);
            if(is_resource($conn))
            {
                if(is_callable($this->onConnect))
                    ($this->onConnect)($this,$conn);
                Event::add($conn,$this->receive());
            }
        };
    }

    public function onReceiveBack($server,$socket,$data)
    {
        go(function ()use ($server,$socket,$data){
            $request = clone app('httpRequest');
            $request->init($data);
            $ctrl = app('route')->match($request);
            if(empty($ctrl))
            {
                $this->send($socket,404);
                unset($request);
                return;
            }
            $class = new $ctrl[0];
            $respond = call_user_func([$class,$ctrl[1]],$request);
            $server->send($socket,$respond);
            unset($request,$class);
        });
    }

    public function receive()
    {
        return function($socket)
        {
            if(!is_resource($socket) or feof($socket))
            {
                swoole_event_del($socket);
                fclose($socket);
                return;
            }
            $data = fread($socket, 1024);
            if(is_callable($this->onReceive))
                ($this->onReceive)($this,$socket,$data);
        };
    }

    public function send($conn, $content){
        $http_resonse = "HTTP/1.1 200 OK\r\n";
        $http_resonse .= "Content-Type: text/html;charset=UTF-8\r\n";
        $http_resonse .= "Connection: keep-alive\r\n";
        $http_resonse .= "Server: php socket server\r\n";
        $http_resonse .= "Content-length: ".strlen($content)."\r\n\r\n";
        $http_resonse .= $content;
        fwrite($conn, $http_resonse);
    }

    public function start()
    {
        $task = app()->make('task',$this);
        $task->forkTask();
        $worker = app()->make('worker',$this);
        $worker->forkWorker();
        $worker->wait();
    }
}