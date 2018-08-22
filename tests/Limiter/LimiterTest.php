<?php
/**
 * Created by PhpStorm.
 * User: ramil
 * Date: 8/22/18
 * Time: 9:11 PM
 */

namespace Ramil8090\RateLimiter\Tests\Limiter;

use PHPUnit\Framework\TestCase;
use Ramil8090\RateLimiter\Limiter\Limiter;
use Ramil8090\RateLimiter\Request\RequestBuilder;

class LimiterTest extends TestCase
{
    protected function setUp()
    {
        parent::setUp();
        $client = new \Predis\Client();
        $client->flushdb();
    }

    public function testSuccessful()
    {
        $request = RequestBuilder::create()
            ->withIp('127.0.0.1')
            ->withUserAgent('User-Agent')
            ->withUrl('/')
            ->build();
        $limiter = new Limiter($request);
        $capacityLimit = 100;
        $limiter->setCapacityLimit($capacityLimit);

        $this->assertTrue($limiter->checkRequestConfirmed());
        for ($i = 0; $i < $capacityLimit; $i++)
        {
            $limiter->checkRequestConfirmed();
        }
        $this->assertFalse($limiter->checkRequestConfirmed());
    }

    public function testHighRateLimit()
    {
        $request = RequestBuilder::create()
            ->withIp('127.0.0.1')
            ->withUserAgent('User-Agent')
            ->withUrl('/')
            ->build();
        $limiter = new Limiter($request);
        $capacityLimit = 100;
        $limiter->setCapacityLimit($capacityLimit);
        $limiter->setRateLimit(100, 1); // 100 requests per second

        $this->assertTrue($limiter->checkRequestConfirmed());
        for ($i = 0; $i < $capacityLimit; $i++)
        {
            $limiter->checkRequestConfirmed();
        }
        sleep(1);
        $this->assertTrue($limiter->checkRequestConfirmed());
    }

    public function testLimitPerHour()
    {
        $request = RequestBuilder::create()
            ->withIp('127.0.0.1')
            ->withUserAgent('User-Agent')
            ->withUrl('/')
            ->build();
        $limiter = new Limiter($request);

        $capacityLimit = 100;
        $limiter->setCapacityLimit($capacityLimit);

        $requestCount = 100;
        $duration = 60;
        $rateLimit = $requestCount / $duration;
        $hourLimit = floor($rateLimit * 60 * 60);
        $limiter->setRateLimit($requestCount, $duration); // 100 requests per minute

        $this->assertEquals($hourLimit, $limiter->getLimitPerHour());
    }

    public function testRemaining()
    {
        $request = RequestBuilder::create()
            ->withIp('127.0.0.1')
            ->withUserAgent('User-Agent')
            ->withUrl('/')
            ->build();
        $limiter = new Limiter($request);

        $this->assertEquals(100, $limiter->getRemaining());
    }
}