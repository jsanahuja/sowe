<?php

namespace Sowe\HTTP;

class JsonResponse extends Response
{
    public function __construct(){
        $this->code = 200;
        $this->headers = ["Content-Type" => "application/json"];
        $this->body = "";
    }

    public function writeBody($body){
        $this->body = json_encode($body);
    }

}
