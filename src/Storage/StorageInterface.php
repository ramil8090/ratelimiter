<?php
/**
 * Created by PhpStorm.
 * User: ramil
 * Date: 8/22/18
 * Time: 3:20 PM
 */

namespace Ramil8090\RateLimiter\Storage;


interface StorageInterface
{
    public function __construct(array $config);
    public function get(string $key) : ?string;
    public function set(string $key, string $value) : void;
}