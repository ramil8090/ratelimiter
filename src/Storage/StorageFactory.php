<?php
/**
 * Created by PhpStorm.
 * User: ramil
 * Date: 8/22/18
 * Time: 5:33 PM
 */

namespace Ramil8090\RateLimiter\Storage;


class StorageFactory
{
    const REDIS = 'redis';

    public static function get(string $storage, array $config) : StorageInterface
    {
        if ($storage === static::REDIS) {
            return new RedisStorage($config);
        }

        throw new \InvalidArgumentException('Unknown storage given');
    }
}