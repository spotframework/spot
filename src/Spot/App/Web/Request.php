<?php
namespace Spot\App\Web;

class Request extends \ArrayObject {
    const GET = "GET";
    const PUT = "PUT";
    const POST = "POST";
    const HEAD = "HEAD";
    const PURGE = "PURGE";
    const DELETE = "DELETE";
    const OPTIONS = "OPTIONS";

    private $method,
            $uri,
            $body,
            $POST,
            $QUERY,
            $FILES,
            $COOKIE,
            $HEADERS,
            $httpVersion;

    public function __construct(
            $method,
            $uri,
            $httpVersion,
            $body,
            array $QUERY,
            array $POST,
            array $PAYLOAD,
            array $FILES,
            array $COOKIE,
            array $HEADERS) {
        parent::__construct($PAYLOAD + $POST + $QUERY, \ArrayObject::STD_PROP_LIST);

        $this->method = $method;
        $this->uri = $uri;
        $this->body = $body;
        $this->POST = $POST;
        $this->QUERY = $QUERY;
        $this->FILES = $FILES;
        $this->COOKIE = $COOKIE;
        $this->HEADERS = $HEADERS;
        $this->httpVersion = $httpVersion;
    }

    public function method() {
        return $this->method;
    }

    public function uri() {
        return $this->uri;
    }

    public function headers() {
        return $this->HEADERS;
    }

    public function header($name) {
        if(isset($this->HEADERS[$name])) {
            return $this->HEADERS[$name];
        }
    }

    public function cookies() {
        return $this->COOKIE;
    }

    public function cookie($name) {
        if(isset($this->COOKIE[$name])) {
            return $this->COOKIE[$name];
        }
    }

    public function files() {
        return $this->FILES;
    }

    public function file($name) {
        if(isset($this->FILES[$name])) {
            return $this->FILES[$name];
        }
    }

    public function body() {
        return $this->body;
    }

    public function length() {
        return $this->header("Content-Length");
    }

    public function isAjax() {
        return $this->header("X-Requested-With") === "XMLHttpRequest";
    }

    public function __toString() {
        $req = "{$this->method} {$this->uri}";
        $req .= ($this->QUERY ? "?".http_build_query($this->QUERY) : "");
        $req .= " {$this->httpVersion}";
        $this->body && $req .= "\r\n{$this->body}";

        return $req;
    }

    static public function create(
            $method = Request::GET,
            $uri = "/",
            $httpVersion = "HTTP/1.1",
            $body = null,
            $query = [],
            $post = [],
            $files = [],
            $cookie = [],
            $headers = []) {
        $payload = [];
        if(isset($headers["Content-Type"])) {
            $contentType = $headers["Content-Type"];
            if(strpos($contentType, "application/json") !== false) {
                $payload = json_decode($body, true);
            }
        }

        return new Request(
            $method,
            "/".trim(urldecode($uri), "/"),
            $httpVersion,
            $body,
            $query,
            $post,
            $payload,
            $files,
            $cookie,
            $headers
        );
    }

    static public function createFromGlobals() {
        return self::create(
            $_SERVER["REQUEST_METHOD"],
            //remove query string from uri if exists
            ($pos = strpos($_SERVER["REQUEST_URI"], "?"))
                ? substr($_SERVER["REQUEST_URI"], 0, $pos)
                : $_SERVER["REQUEST_URI"],
            $_SERVER["SERVER_PROTOCOL"],
            file_get_contents("php://input"),
            $_GET,
            $_POST,
            $_FILES,
            $_COOKIE,
            getallheaders()
        );
    }
}
