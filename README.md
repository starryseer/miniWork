# miniWork
用laravel ioc模式封装了 原生workerman 实现了简易版的http服务器

目录结构
|-app
|----Http
|--------Controller
|-bin
|----start.php
|-config
|----config.php
|-route
|----http.php

整个框架。
采用epoll模型，
主进程对子进程进行进程回收，
work进程监听socket请求，并处理请求返回。
task进程通过管道，接受worker进程发送的数据，用于处理耗时任务。


启动方式：进入bin目录，php start.php
配置文件：config.php，自定义配置文件如：test.php，可以通过，app('config')->get('test')进行获取
路由方式：route下的http.php，类似laravel集成，支持控制器和回调函数的方式。
redis缓存：app('redis')->invoke,进行redis连接池获取，redis协程连接池会进行自动回收。
整个request采用协程模式，支持swoole的协程方法。
用于个人学习框架学习。

v2版本计划
添加Mysql协程连接池，http协程请求，websocket封装，http服务器完善。


