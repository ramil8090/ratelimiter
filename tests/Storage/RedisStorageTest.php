<?php

namespace Ramil8090\RateLimiter\Tests\Storage;

use Ramil8090\RateLimiter\Storage\RedisStorage;
use PHPUnit\Framework\TestCase;

class RedisStorageTest extends TestCase
{
    /**
     * @expectedException \ArgumentCountError
     */
    public function testNotEmptyConstructor()
    {
        new RedisStorage();
    }

    /**
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage Invalid config.
     */
    public function testEmptyConfig()
    {
        $config = [];
        new RedisStorage($config);
    }

    /**
     * @expectedException \Exception
     */
    public function testInvalidConfig()
    {
        $config = [
            'parameters' => [
                'host' => 'host',
                'port' => 'port'
            ],
            'options' => []
        ];
        new RedisStorage($config);
    }

    /**
     * @expectedException \DomainException
     * @expectedExceptionMessage Can't connect to redis server.
     */
    public function testInvalidConnect()
    {
        new RedisStorage([
            'parameters' => [
                'host' => $host = '127.0.0.1',
                'port' => $port = -1
            ],
            'options' => []
        ]);
    }

    public function testGetNull()
    {
        $storage = new RedisStorage([
            'parameters' => [
                'host' => $host = '127.0.0.1',
                'port' => $port = 6379
            ],
            'options' => []
        ]);

        $value = $storage->get('not_exist_key');
        $this->assertNull($value);
    }

    public function testSuccessfulSet()
    {
        $storage = new RedisStorage([
            'parameters' => [
                'host' => $host = '127.0.0.1',
                'port' => $port = 6379
            ],
            'options' => []
        ]);

        $storage->set($key = 'test', $value = 'testValue');
        $getValue = $storage->get($key);
        $this->assertEquals($value, $getValue);
    }
}
