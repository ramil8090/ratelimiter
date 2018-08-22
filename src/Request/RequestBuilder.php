<?php
/**
 * Created by PhpStorm.
 * User: ramil
 * Date: 8/22/18
 * Time: 6:56 PM
 */

namespace Ramil8090\RateLimiter\Request;


use Ramil8090\RateLimiter\Storage\StorageFactory;
use Ramil8090\RateLimiter\Storage\StorageInterface;

class RequestBuilder
{
    private $ip;
    private $userAgent;
    private $url;
    private $storage;
    private $client;
    private static $instance;

    public static function create() : RequestBuilder
    {
        if (null === static::$instance) {
            static::$instance = new static();
        }

        return static::$instance;
    }

    public function withIp(string $ip) : RequestBuilder
    {
        $this->ip = $ip;
        return $this;
    }

    public function withUserAgent(string $userAgent) : RequestBuilder
    {
        $this->userAgent = $userAgent;
        return $this;
    }

    public function withUrl(string $url) :  RequestBuilder
    {
        $this->url = $url;
        return $this;
    }

    public function withStorage(StorageInterface $storage) : RequestBuilder
    {
        $this->storage = $storage;
        return $this;
    }

    public function withClient(Client $client) : RequestBuilder
    {
        $this->client = $client;
        return $this;
    }

    public function build() : Request
    {
        if (!$this->url) {
            $this->url = $_SERVER['REQUEST_URI'];
        }

        if (!$this->ip) {
            $this->ip = $_SERVER['REMOTE_ADDR'];
        }

        if (!$this->userAgent) {
            $this->userAgent = $_SERVER['HTTP_USER_AGENT'];
        }

        if (!$this->storage) {
           $this->storage = StorageFactory::get(StorageFactory::REDIS, [
               'parameters' => [],
               'options' => []
           ]);
        }

        if (!$this->client) {
            $this->client = new Client(
                $this->ip,
                $this->userAgent
            );
        }

        return new Request(
            $this->url,
            $this->client,
            $this->storage
        );
    }

    private function __construct()
    {
    }

    private function clone()
    {
    }

    private function __wakeup()
    {
    }
}