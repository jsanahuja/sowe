<?php

namespace Sowe\HTTP;

class Response
{
    protected $code;
    protected $headers;
    protected $body;

    public function __construct(){
        $this->code = 200;
        $this->headers = [];
        $this->body = "";
    }

    public function writeBody($body){
        $this->body = $body;
    }

    public function setHeader($header, $value){
        $this->headers[$header] = $value;
    }

    public function setStatusCode($code){
        $this->code = $code;
    }

    public function emit(){
        http_response_code($this->code);
        foreach($this->headers as $header => $value){
            header($header . ": " . $value);
        }
        echo $this->body;
    }
}
