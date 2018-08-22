<?php
/**
 * Created by PhpStorm.
 * User: ramil
 * Date: 8/22/18
 * Time: 8:22 PM
 */

namespace Ramil8090\RateLimiter\Limiter;


use Ramil8090\RateLimiter\Request\RequestInterface;

class Limiter
{
    /**
     * @var RequestInterface
     */
    private $request;
    /**
     * @var int
     */
    private $capacityLimit = 100; // Max capacity is 100 requests
    /**
     * @var float|int
     */
    private $rateLimit = 100 / 60; // 100 requests per minute

    public function __construct(RequestInterface $request)
    {
        $this->request = $request;
    }

    /**
     * Check the request for over limit
     * Use leaky bucket algorithm (https://en.wikipedia.org/wiki/Leaky_bucket)
     * @return bool
     */
    public function checkRequestConfirmed(): bool
    {
        $currentRequestTime = time();
        $lastConfirmedRequestTime = $this->request->getLastConfirmedTime();
        $capacity = $this->request->getCapacity();
        $capacityLimit = $this->capacityLimit;
        $rateLimit = $this->rateLimit;

        $capacity = $capacity - ($rateLimit * ($currentRequestTime - $lastConfirmedRequestTime));

        if ($capacity < 0) {
            $capacity = 0;
        } else {
            if ($capacity > $capacityLimit) {
                return false;
            }
        }

        $this->request->updateCapacity($capacity + 1);
        $this->request->updateLastConfirmedTime($currentRequestTime);
        return true;
    }

    /**
     * @param int $capacityLimit
     */
    public function setCapacityLimit(int $capacityLimit): void
    {
        $this->capacityLimit = $capacityLimit;
    }

    /**
     * Calculate rate limit by formula $requestCount / $duration
     * @param int $requestCount
     * @param int $duration in seconds
     */
    public function setRateLimit(int $requestCount, int $duration): void
    {
        $this->rateLimit = $requestCount / $duration;
    }

    /**
     * Return requests limit per hour. Can use in rate limiting http headers
     * (Example, X-RateLimit-Limit: 100)
     * @return int
     */
    public function getLimitPerHour(): int
    {
        return floor($this->rateLimit * 60 * 60);
    }

    /**
     * Return the number of requests left. Can use in rate limiting http headers
     * (Example, X-RateLimit-Remaining: 100 )
     * @return int
     */
    public function getRemaining(): int
    {
        return floor($this->capacityLimit - $this->request->getCapacity());
    }
}