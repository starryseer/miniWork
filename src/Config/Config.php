<?php
/**
 * Created by PhpStorm.
 * User: EDZ
 * Date: 2020/3/11
 * Time: 17:26
 */

namespace Starryseer\Work\Config;


class Config
{
    private $configPath;
    private $config;

    public function __construct()
    {
        $this->configPath = app()->getBasePath()."/config";
        $this->phpParser();
    }

    public function phpParser()
    {
        $files = scandir($this->configPath);
        foreach($files as $file)
        {
            if($file == '.' or $file == '..')
                continue;

            $pathInfo = pathinfo($file);
            if($pathInfo['extension'] == 'php')
            {
                $this->config[explode('.php',$pathInfo['basename'])[0]] = require_once $this->configPath.'/'.$file;
            }
        }
    }

    public function get($key='')
    {
        if(empty($key))
        {
            return $this->config;
        }
        else if(strpos($key,'.') !== false)
        {
            $keys = explode('.',$key);
            $data = $this->config;
            foreach($keys as $key)
            {
                if(isset($data[$key]))
                    $data = $data[$key];
                else
                    return null;
            }
        }
        else
        {
            if(isset($this->config[$key]))
                return $this->config[$key];
            else
                return null;
        }

    }




}