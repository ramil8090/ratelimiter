<?php
/**
 * Created by PhpStorm.
 * User: ramil
 * Date: 8/22/18
 * Time: 2:29 PM
 */

namespace Ramil8090\RateLimiter\Storage;


use Predis\Client;
use Predis\Connection\ConnectionException;

class RedisStorage implements StorageInterface
{
    /**
     * @var Client
     */
    private $client;

    /**
     * @var array
     */
    private $config;

    public function __construct(array $config)
    {
        $this->config = $config;
        $this->connect();
    }

    public function connect()
    {
        if ($this->isInvalidConfig()) {
            throw new \InvalidArgumentException("Invalid config.");
        }

        try {
            $parameters = $this->config['parameters'];
            $options = $this->config['options'];

            $this->client = new Client($parameters, $options);
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }

        if (!$this->isConnected()) {
            throw new \DomainException("Can't connect to redis server.");
        }
    }

    private function isConnected()
    {
        try {
            $testKey = 'testConnection';
            $this->client->set($testKey, 'testConnection');
            $this->client->del([$testKey]);
        } catch (ConnectionException $e) {
            return false;
        }

        return true;
    }

    private function isInvalidConfig()
    {
        return !array_key_exists('parameters', $this->config) ||
            !array_key_exists('options', $this->config);
    }

    public function get(string $key): ?string
    {
        return $this->client->get($key);
    }

    public function set(string $key, string $value): void
    {
        $this->client->set($key, $value);
    }
}