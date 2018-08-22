<?php
/**
 * Created by PhpStorm.
 * User: ramil
 * Date: 8/22/18
 * Time: 5:47 PM
 */

namespace Ramil8090\RateLimiter\Tests\Storage;


use PHPUnit\Framework\TestCase;
use Ramil8090\RateLimiter\Storage\RedisStorage;
use Ramil8090\RateLimiter\Storage\StorageFactory;

class StorageFactoryTest extends TestCase
{
    /**
     * @expectedException \InvalidArgumentException
     */
    public function testInvalidStorage()
    {
        StorageFactory::get('some_factory', []);
    }

    public function testSuccessful()
    {
        $config = [
            'parameters' => [],
            'options' => []
        ];
        $storage = StorageFactory::get(StorageFactory::REDIS, $config);
        $this->assertInstanceOf(RedisStorage::class, $storage);
    }
}