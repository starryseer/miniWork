<?php


namespace App\Http\Controller;


use Starryseer\Work\Request\Request;

class IndexController
{
    public function index(Request $request)
    {
        $res = app('redis')->invoke(function ($client){
            return $client->get('room');
        });
        return json_encode($res,true);
    }
}