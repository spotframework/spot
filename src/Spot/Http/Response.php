<?php
namespace Spot\Http;

class Response {
    const OK = 200;
    const CREATED = 201;
    const ACCEPTED = 202;
    const NO_CONTENT = 204;
    
    const MOVED_PERMANENTLY = 301;
    const FOUND = 302;
    const NOT_MODIFIED = 304;
    const TEMPORARY_REDIRECT = 307;
    
    const BAD_REQUEST = 400;
    const UNAUTHORIZED = 401;
    const FORBIDDEN = 403;
    const NOT_FOUND = 404;
    const METHOD_NOT_ALLOWED = 405;
    const NOT_ACCEPTABLE = 406;
    const REQUEST_TIMEOUT = 408;
    
    const INTERNAL_SERVER_ERROR = 500;
    const NOT_IMPLEMENTED = 501;
    const BAD_GATEWAY = 502;
    const SERVICE_UNAVAILABLE = 503;
    const GATEWAY_TIMEOUT = 504;
    
    static private $names = [        
        self::OK => 'OK',
        self::CREATED => 'Created',
        self::ACCEPTED => 'Accepted',
        self::NO_CONTENT => 'No Content',
        
        self::MOVED_PERMANENTLY => 'Moved Permanently',
        self::FOUND => 'Found',
        self::NOT_MODIFIED => 'Not Modified',
        self::TEMPORARY_REDIRECT => 'Temporary Redirect',
        
        self::BAD_REQUEST => 'Bad Request',
        self::UNAUTHORIZED => 'Unauthorized',
        self::FORBIDDEN => 'Forbidden',
        self::NOT_FOUND => 'Not Found',
        self::METHOD_NOT_ALLOWED => 'Method Not Allowed',
        self::NOT_ACCEPTABLE => 'Not Acceptable',
        self::REQUEST_TIMEOUT => 'Request Timeout',
        
        self::INTERNAL_SERVER_ERROR => 'Internal Server Error',
        self::NOT_IMPLEMENTED => 'Not Implemented',
        self::BAD_GATEWAY => 'Bad Gateway',
        self::SERVICE_UNAVAILABLE => 'Service Unavailable',
        self::GATEWAY_TIMEOUT => 'Gateway Timeout',
    ];
    
    private $header = [],
            $content;
    
    public function setHeader($type, $value) {
        $this->header[$type] = $value;
    }
    
    public function getHeader($type) {
        if(isset($this->header[$type])) {
            return $this->header[$type];
        }
    }
    
    public function setHttpCode($code) {
        if(!isset(self::$names[$code])) {
            throw new \InvalidArgumentException("Http code {$code} is invalid");   
        }
        
        $this->setHeader('HTTP/1.1', $code.' '.self::$names[$code]);
    }
    
    public function getHttpCode() {
        return $this->getHeader('HTTP/1.1');
    }
    
    public function setContentType($type) {
        $this->setHeader('Content-Type:', $type);
    }
    
    public function getContentType() {
        return $this->getHeader('Content-Type:');
    }
    
    public function setLocation($location) {
        $this->setHeader('Location:', $location);
    }
    
    public function getLocation() {
        return $this->getHeader('Location:');
    }
    
    public function setContent($content) {
        $this->content = $content;
    }
    
    public function getContent() {
        return $this->content;
    }
    
    public function redirect($location, $permanent = false) {
        $this->setLocation($location);
        $this->setHttpCode($permanent ? Response::MOVED_PERMANENTLY : Response::TEMPORARY_REDIRECT);
    }
    
    public function flush() {
        foreach($this->header as $type => $value) {
            header($type.' '.$value);
        }
        
        echo $this->content;
    }
}