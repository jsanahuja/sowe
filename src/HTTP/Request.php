<?php

namespace Sowe\HTTP;

class Request
{
    public $ip;
    public $headers;
    public $variables;
    public $files;
    public $body;

    public function __construct()
    {
        $this->responseFormats = [];

        $this->get_ip();
        $this->parse_request();
    }

    /** Get Origin IP */
    private function get_ip()
    {
        if (isset($_SERVER['HTTP_CF_CONNECTING_IP']) && !empty($_SERVER['HTTP_CF_CONNECTING_IP'])) {
            $this->ip = $_SERVER['HTTP_CF_CONNECTING_IP'];
        } elseif (isset($_SERVER['HTTP_CLIENT_IP']) && !empty($_SERVER['HTTP_CLIENT_IP'])) {
            $this->ip = $_SERVER['HTTP_CLIENT_IP'];
        } elseif (isset($_SERVER['HTTP_X_FORWARDED_FOR']) && !empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $this->ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
        } elseif (isset($_SERVER['REMOTE_ADDR']) && !empty($_SERVER['REMOTE_ADDR'])) {
            $this->ip = $_SERVER['REMOTE_ADDR'];
        } else {
            $this->ip = "Unknown";
        }
    }

    private function parse_request(){
        // Headers
        $this->headers = array_change_key_case(apache_request_headers(), CASE_LOWER);
        $this->variables = [];
        $this->files = [];

        // GET Variables
        $this->variables = $_GET;
        // POST Variables
        $this->variables = array_merge($this->variables, $_POST);
        // Files
        $this->variables = array_merge($this->variables, $_FILES);
        // Body
        $this->body = file_get_contents('php://input');
    }
    
}
