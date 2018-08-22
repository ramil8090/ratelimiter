#RateLimiter
Request rate limiter based on leaky bucket algorithm (https://en.wikipedia.org/wiki/Leaky_bucket) and use
Redis as a storage.

##Installation
Not allowed yet.

##Usage
###Overview
```php
// 1. Create request.
$request = RequestBuilder::create()->build();
// 2. Init Limiter
$limiter = new Limiter($request);
// 3. Check request for over limit
if ($limiter->checkRequestConfirmed()) {
    // Request confirmed
} else {
    // Request unconfirmed
} 
// 4. Get rate limiter data for http headers (optional)
header('X-RateLimit-Limit: ' . $limiter->getLimitPerHour());
header('X-RateLimit-Remaining' . $limiter->getRemaining());
```
###Configuration
####Storage
Setting storage is optional. As a storage use Redis server and nrk/predis (https://github.com/nrk/predis)
as driver. By default, for connection, use scheme 'tcp', host '127.0.0.1', port '6379'. You can pass 
any parameter and options from predis package.
```php
// Setting storage (optional)
$storage = StorageFactory::get(StorageFactory::REDIS, [
            'parameters' => [
                'scheme' => 'tcp',
                'host' => '127.0.0.1',
                'port => '6379'
            ],
            'options' => [
                'parameters' => [
                    'password' => $secretpassword,
                    'database' => 10,
                ],
            ]
        ]);
```
####Request
Request setting is optional. By default, use $_SERVER variables and storage with default settings.
```php
// Request
RequestBuilder::create()
            ->withIp('127.0.0.1')
            ->withUserAgent('User-Agent')
            ->withUrl('/')
            ->withStorage($storage)
            ->build();
```
##Author

Ramil Ziganshin

##Contributing

Please feel free to send pull requests.
