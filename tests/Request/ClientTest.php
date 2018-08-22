<?php
/**
 * Created by PhpStorm.
 * User: ramil
 * Date: 8/22/18
 * Time: 6:07 PM
 */

namespace Ramil8090\RateLimiter\Tests\Request;

use PHPUnit\Framework\TestCase;
use Ramil8090\RateLimiter\Request\Client;

class ClientTest extends TestCase
{
    public function testSuccessful()
    {
        $client = new Client(
            $ip = '127.0.0.1',
            $userAgent = 'User-Agent string'
        );

        $this->assertEquals($ip, $client->getIp());
        $this->assertEquals($userAgent, $client->getUserAgent());
    }
}