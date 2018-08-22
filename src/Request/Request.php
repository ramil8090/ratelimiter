<?php
/**
 * Created by PhpStorm.
 * User: ramil
 * Date: 8/22/18
 * Time: 6:15 PM
 */

namespace Ramil8090\RateLimiter\Request;


use Ramil8090\RateLimiter\Storage\StorageInterface;

class Request implements RequestInterface
{
    /**
     * @var string
     */
    private $key;
    /**
     * @var string
     */
    private $url;
    /**
     * @var ?integer
     */
    private $lastConfirmedTime;
    /**
     * @var float
     */
    private $capacity;
    /**
     * @var StorageInterface
     */
    private $storage;
    /**
     * @var Client
     */
    private $client;


    public function __construct(string $url, Client $client, StorageInterface $storage)
    {
        $this->url = $url;
        $this->client = $client;
        $this->storage = $storage;

        if (!$this->loadStoredRequestData()) {
            $this->createRequestDataInStorage();
        }
    }

    /**
     * Load capacity and lastConfirmedTime of request from storage. Use in Limiter.
     */
    private function loadStoredRequestData() : bool
    {
        $this->generateKey();

        $storedRequestData = unserialize($this->storage->get($this->key));
        if ($storedRequestData) {
            $this->lastConfirmedTime = $storedRequestData['lastConfirmedTime'];
            $this->capacity = $storedRequestData['capacity'];
            return true;
        }

        return false;
    }

    /**
     * Need to find and update request data in storage
     */
    private function generateKey() : void
    {
        $ip = $this->client->getIp();
        $userAgent = $this->client->getUserAgent();
        $url = $this->url;

        $this->key = md5($ip.$userAgent.$url);
    }

    /**
     * Save init values of capacity and lastConfirmedTime in storage.
     */
    private function createRequestDataInStorage()
    {
        $this->lastConfirmedTime = null;
        $this->capacity = 0;

        $requestData = serialize([
            'lastConfirmedTime' => $this->lastConfirmedTime,
            'capacity' => $this->capacity
        ]);

        $this->storage->set($this->key, $requestData);
    }

    /**
     * @return string
     */
    public function getKey(): string
    {
        return $this->key;
    }

    /**
     * @return string
     */
    public function getUrl(): string
    {
        return $this->url;
    }

    /**
     * @return int
     */
    public function getLastConfirmedTime(): ?int
    {
        return $this->lastConfirmedTime;
    }

    /**
     * @return float
     */
    public function getCapacity(): float
    {
        return $this->capacity;
    }

    /**
     * @return Client
     */
    public function getClient(): Client
    {
        return $this->client;
    }

    /**
     * Update capacity in storage and set property value
     * @param float $capacity
     */
    public function updateCapacity($capacity): void
    {
        $this->updateProperty('capacity', $capacity);
    }

    /**
     * Update lastConfirmedTime in storage and set property value
     * @param integer $timestamp
     */
    public function updateLastConfirmedTime($timestamp): void
    {
        $this->updateProperty('lastConfirmedTime', $timestamp);
    }

    /**
     * Update property in storage and instance
     * @param string $property (capacity | lastConfirmedTime)
     * @param mixed $value
     */
    private function updateProperty(string $property, $value)
    {
        $storedRequestData = unserialize($this->storage->get($this->key));
        if ($storedRequestData) {
            $storedRequestData[$property] = $value;

            $storedRequestData = serialize($storedRequestData);
            $this->storage->set($this->key, $storedRequestData);

            $this->{$property} = $value;
        }
    }
}