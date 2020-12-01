<?php

namespace Sowe\HTTP;

use Sowe\HTTP\Exceptions\{NotFound, MethodNotAllowed};

class Router
{
    protected $url;
    protected $routes;
    protected $arguments;

    public function __construct($base = "")
    {
        $this->routes = [];
        $this->parse_url($_SERVER['REQUEST_URI'], $base);
    }

    private function parse_url($url, $base)
    {
        $this->url = implode("/", array_diff(
            array_filter(explode("/", explode("?", $url)[0])),
            array_filter(explode("/", $base))
        ));
    }

    public function on($method, $route, $controller, ...$arguments)
    {
        $this->routes[] = new Route($method, $route, $controller, $arguments);
        return $this;
    }

    public function route()
    {
        $urlArgs = array_values(array_filter(explode("/", $this->url)));
        $method = strtolower($_SERVER['REQUEST_METHOD']);
        $matching = [];

        foreach($this->routes as $route){
            $routeArgs = array_values(array_filter(explode("/", $route->getRoute())));
            $arguments = [];
            
            // Not the same arg length. Skipping route
            if(sizeof($routeArgs) != sizeof($urlArgs)){
                continue;
            }

            for($i = 0; $i < sizeof($urlArgs); $i++){
                if($routeArgs[$i] == "%"){
                    // Wilcard argument
                    $arguments[] = $urlArgs[$i];
                }else if($routeArgs[$i] !== $urlArgs[$i]){
                    // Argument not matching. Skipping route
                    continue 2;
                }
            }

            // Here we can say the route matches.
            $matching[$route->getMethod()] = [
                'route'     => $route, 
                'arguments' => $arguments
            ];
        }

        if(sizeof($matching) == 0){
            // 404
            throw new NotFound();
        }else if($method == "options"){
            http_response_code(204);
            header("Allow: ". implode(", ", array_keys($matching)));
        }else if(!isset($matching[$method])){
            // 405
            throw new MethodNotAllowed();
        }else{
            array_unshift(
                $matching[$method]['arguments'], 
                new Request()
            );
            $matching[$method]['route']->dispatch(
                $matching[$method]['arguments']
            )->emit();
        }
    }
}
