<?php
namespace Spot\Http;

use ArrayAccess;
use IteratorAggregate;
use Spot\Http\Request\Header;
use Spot\Http\Request\Uri;
use Spot\Http\Request\Get;
use Spot\Http\Request\Post;
use Spot\Http\Request\Files;
use Spot\Http\Request\Cookie;
use Spot\Http\Request\Server;
use Spot\Http\Request\Body;
use Spot\Http\Request\Path;

class Request implements ArrayAccess, IteratorAggregate {
    const GET = "GET";
    const PUT = "PUT";
    const POST = "POST";
    const HEAD = "HEAD";
    const PURGE = "PURGE";
    const TRACE = "TRACE";
    const DELETE = "DELETE";
    const OPTIONS = "OPTIONS";
    
    private $uri;
    
    public $query;
    public $post;
    public $cookie;
    public $files;
    public $server;
    public $header;
    public $path;
    public $body;
    
    public function __construct(
            Uri $uri,
            Get $query,
            Post $post,
            Files $files,
            Cookie $cookie,
            Server $server,
            Body $body) {
        $this->uri = $uri;
        $this->query = $query;
        $this->post = $post;
        $this->files = $files;
        $this->cookie = $cookie;
        $this->server = $server;
        $this->header = new Header($server);
        $this->body = $body;
        $this->path = new Path();
    }
    
    public function offsetExists($index) {
        return
            isset($this->files[$index])
            ||
            isset($this->path[$index])
            ||
            isset($this->post[$index])
            ||
            isset($this->query[$index]);
    }
    
    public function offsetGet($index) {
        static $vars = ["files", "path", "post", "query"];
        foreach($vars as $var) {
            if(isset($this->{$var}[$index])) {
                return $this->{$var}[$index];
            }
        }
        
        return null;
    }
    
    public function offsetSet($index, $value) {
        throw new \LogicException('Request object is immutable.');
    }
    
    public function offsetUnset($offset) {
        throw new \LogicException('Request object is immutable.');
    }
    
    public function getUri() {
        return $this->uri;
    }
    
    public function getMethod() {
        return $this->server["REQUEST_METHOD"];
    }
    
    public function getUserAgent() {
        return $this->header["USER_AGENT"];
    }
    
    public function getHost() {
        return $this->server["HTTP_HOST"];
    }
    
    public function isAjax() {
        return 
            isset($this->header["X_REQUESTED_WITH"])
            &&
            strtolower($this->header["X_REQUESTED_WITH"]) === "xmlhttprequest";
    }

    public function getIterator() {
        $i = new \AppendIterator();

        $i->append($this->files->getIterator());
        $i->append($this->path->getIterator());
        $i->append($this->post->getIterator());
        $i->append($this->query->getIterator());

        return $i;
    }
    
    public function __toString() {
        return "http://".$this->server["HTTP_HOST"].$this->uri.$this->query;
    }
    
    static public function createFromGlobal() {
        $body = [];
        if( $_SERVER['REQUEST_METHOD'] === self::PUT 
            || 
            $_SERVER['REQUEST_METHOD'] === self::POST) {
            parse_str($input = file_get_contents('php://input'), $body);
        }

        return self::create(
            //remove query string if exists
            $_GET
                ? substr($_SERVER['REQUEST_URI'], 0, -strlen($_SERVER['QUERY_STRING']) - 1) 
                : $_SERVER['REQUEST_URI'],
            $_GET,
            $_POST,
            $_FILES,
            $_COOKIE,
            $_SERVER,
            $body
        );
    }
    
    static public function create(
            $uri = '/',
            array $query = [], 
            array $post = [], 
            array $files = [], 
            array $cookie = [], 
            array $server = [],
            array $body = []) {

        return new self(
            new Uri($uri),
            new Get($query),
            new Post($post),
            new Files($files),
            new Cookie($cookie),
            new Server($server),
            new Body($body)
        );
    }
}