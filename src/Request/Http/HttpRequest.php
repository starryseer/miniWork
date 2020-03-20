<?php
/**
 * Created by PhpStorm.
 * User: EDZ
 * Date: 2020/3/11
 * Time: 18:01
 */

namespace Starryseer\Work\Request\Http;

use Starryseer\Work\Request\Request;

class HttpRequest extends Request
{
    private $header;
    private $uri;
    private $path;
    private $method;
    private $params;
    private $cookie;
    private $protocol;
    private $buffer;


    public function init($buffer)
    {
        $this->buffer = $buffer;
        // Parse headers.
        list($http_header, $http_body) = \explode("\r\n\r\n", $buffer, 2);
        $header_data = \explode("\r\n", $http_header);

        list($this->method, $this->uri, $this->protocol) = \explode(' ',
            $header_data[0]);

        $this->path = parse_url($this->uri, \PHP_URL_PATH);
        if($this->method == 'GET')
        {
            \parse_str(\parse_url($this->uri, \PHP_URL_QUERY),$this->params);
        }
    }

    public function getMethod()
    {
        return $this->method;
    }

    public function getParams()
    {
        return $this->params;
    }

    public function getPath()
    {
        return $this->path;
    }
}