<?php
/**
 * Created by PhpStorm.
 * User: ramil
 * Date: 8/22/18
 * Time: 8:24 PM
 */

namespace Ramil8090\RateLimiter\Request;


interface RequestInterface
{
    public function getLastConfirmedTime() : ?int;
    public function getCapacity() : float;
    public function updateCapacity($capacity) : void;
    public function updateLastConfirmedTime($timestamp) : void;

}