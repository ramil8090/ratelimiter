<?php
/**
 * Created by PhpStorm.
 * User: ramil
 * Date: 8/22/18
 * Time: 6:15 PM
 */

namespace Ramil8090\RateLimiter\Tests\Request;


use PHPUnit\Framework\TestCase;
use Ramil8090\RateLimiter\Request\Client;
use Ramil8090\RateLimiter\Request\Request;
use Ramil8090\RateLimiter\Request\RequestBuilder;
use Ramil8090\RateLimiter\Storage\StorageFactory;

class RequestTest extends TestCase
{
    protected function setUp()
    {
        parent::setUp();
        $client = new \Predis\Client();
        $client->flushdb();
    }

    public function testSuccessful()
    {
        $storage = StorageFactory::get(StorageFactory::REDIS, [
            'parameters' => [],
            'options' => []
        ]);

        $client = new Client(
            $ip = '127.0.0.1',
            $userAgent = 'User-Agent string'
        );

        $request = new Request(
            $url = '/some-url',
            $client,
            $storage
        );

        $this->assertEquals($ip, $request->getClient()->getIp());
        $this->assertEquals($userAgent, $request->getClient()->getUserAgent());
        $this->assertEquals($url, $request->getUrl());
        $this->assertNull($request->getLastConfirmedTime());
        $this->assertEquals(0, $request->getCapacity());

        $key = md5($ip.$userAgent.$url);
        $this->assertEquals($key, $request->getKey());
    }

    public function testUpdateCapacity()
    {
        $request = RequestBuilder::create()
            ->withIp('127.0.0.1')
            ->withUserAgent('User-Agent')
            ->withUrl('/')
            ->build();

        $oldCapacity = $request->getCapacity();

        $request->updateCapacity($newCapacity = 20);

        $this->assertNotEquals($oldCapacity, $newCapacity);
        $this->assertEquals($newCapacity, $request->getCapacity());
    }

    public function testUpdateLastConfirmedTime()
    {
        $request = RequestBuilder::create()
            ->withIp('127.0.0.1')
            ->withUserAgent('User-Agent')
            ->withUrl('/')
            ->build();

        $oldTime = $request->getLastConfirmedTime();

        $request->updateLastConfirmedTime($newTime = time());

        $this->assertNotEquals($oldTime, $newTime);
        $this->assertEquals($newTime, $request->getLastConfirmedTime());

    }

}