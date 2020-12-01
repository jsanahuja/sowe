<?php

namespace Sowe\HTTP;

class Route
{
    private $method;
    private $route;
    private $controller;
    private $arguments;

    public function __construct($method, $route, $controller, $arguments){
        $this->method       = strtolower($method);
        $this->route        = $route;
        $this->controller   = $controller;
        $this->arguments    = $arguments;
    }

    public function getMethod(){
        return $this->method;
    }

    public function getRoute(){
        return $this->route;
    }

    public function dispatch($arguments){
        $controller = new $this->controller(... $this->arguments);
        return call_user_func_array([$controller, $this->method], $arguments);
    }

}
