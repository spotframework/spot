<?php
namespace Spot\App\Web;

class Response {
    const OK = 200;
    const CREATED = 201;
    const ACCEPTED = 202;
    const NO_CONTENT = 204;

    const MOVED_PERMANENTLY = 301;
    const FOUND = 302;
    const SEE_OTHER = 303;
    const NOT_MODIFIED = 304;
    const USE_PROXY = 305;
    const TEMPORARY_REDIRECT = 307;

    const BAD_REQUEST = 400;
    const UNAUTHORIZED = 401;
    const PAYMENT_REQUIRED = 402;
    const FORBIDDEN = 403;
    const NOT_FOUND = 404;
    const METHOD_NOT_ALLOWED = 405;
    const NOT_ACCEPTABLE = 406;
    const PROXY_AUTHENTICATION_REQUIRED = 407;
    const REQUEST_TIMEOUT = 408;

    const INTERNAL_SERVER_ERROR = 500;
    const NOT_IMPLEMENTED = 501;
    const BAD_GATEWAY = 502;
    const SERVICE_UNAVAILABLE = 503;
    const GATEWAY_TIMEOUT = 504;
    const HTTP_VERSION_NOT_SUPPORTED = 505;

    static private $names = [
        self::OK => "OK",
        self::CREATED => "Created",
        self::ACCEPTED => "Accepted",
        self::NO_CONTENT => "No Content",

        self::MOVED_PERMANENTLY => "Moved Permanently",
        self::FOUND => "Found",
        self::NOT_MODIFIED => "Not Modified",
        self::TEMPORARY_REDIRECT => "Temporary Redirect",

        self::BAD_REQUEST => "Bad Request",
        self::UNAUTHORIZED => "Unauthorized",
        self::FORBIDDEN => "Forbidden",
        self::NOT_FOUND => "Not Found",
        self::METHOD_NOT_ALLOWED => "Method Not Allowed",
        self::NOT_ACCEPTABLE => "Not Acceptable",
        self::REQUEST_TIMEOUT => "Request Timeout",

        self::INTERNAL_SERVER_ERROR => "Internal Server Error",
        self::NOT_IMPLEMENTED => "Not Implemented",
        self::BAD_GATEWAY => "Bad Gateway",
        self::SERVICE_UNAVAILABLE => "Service Unavailable",
        self::GATEWAY_TIMEOUT => "Gateway Timeout",
    ];

    private $body,
            $headers,
            $cookies = [],
            $httpCode,
            $httpVersion;

    public function __construct(
        $httpCode = Response::OK,
        array $headers = [],
        $httpVersion = "HTTP/1.1") {
        $this->headers = $headers;
        $this->httpCode = $httpCode;
        $this->httpVersion = $httpVersion;
    }

    public function __toString() {
        return
            "{$this->httpVersion} {$this->httpCode} ".
            self::$names[$this->httpCode].
            implode("\r\n", $this->headers).
            $this->body;
    }

    public function body() {
        return $this->body;
    }

    public function setHeader($name, $value) {
        $this->headers[$name] = $value;
    }

    public function getHeader($name) {
        if(isset($this->headers[$name])) {
            return $this->headers[$name];
        }
    }

    public function getHeaders() {
        return $this->headers;
    }

    public function setHttpCode($code) {
        if(!isset(self::$names[$code])) {
            throw new \InvalidArgumentException("Http code {$code} is invalid");
        }

        $this->httpCode = $code;
    }

    public function getHttpCode() {
        return $this->httpCode;
    }

    public function setContentType($contentType) {
        $this->setHeader("Content-Type", $contentType);
    }

    public function getContentType() {
        return $this->getHeader("Content-Type");
    }

    public function setLocation($location) {
        $this->setHeader("Location", $location);
    }

    public function getLocation() {
        return $this->getHeader("Location");
    }

    public function redirect($location, $permanent = false) {
        $this->setLocation($location);

        $permanent && $this->setHttpCode(Response::MOVED_PERMANENTLY);
    }

    public function write($s) {
        $this->body .= implode(func_get_args());
    }

    public function getBody() {
        return $this->body;
    }

    public function setCookie(
            $name,
            $value,
            $expire = null,
            $path = null,
            $domain = null,
            $secure = null,
            $httponly = null) {
        $this->cookies[] = func_get_args();
    }

    public function flush() {
        header("HTTP/1.1 {$this->httpCode} ".self::$names[$this->httpCode]);
        foreach($this->headers as $name => $header) {
            header("{$name}: {$header}");
        }

        foreach($this->cookies as $cookie) {
            call_user_func_array("setcookie", $cookie);
        }

        echo $this->body;
    }
}
