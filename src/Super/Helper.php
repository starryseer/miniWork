<?php
/**
 * Created by PhpStorm.
 * User: EDZ
 * Date: 2020/3/11
 * Time: 16:58
 */

use \Starryseer\Work\Foundation\Application;

if(!function_exists('app'))
{
    function app($abstract=null)
    {
        if(empty($abstract))
            $instance = Application::getInstance();
        else
        {
            $instance = Application::getInstance()->make($abstract);
        }
        return $instance;
    }
}