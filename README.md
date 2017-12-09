LongLog PHP SDK
===================
PHP API client for LongLog application<br>

Installation
---
Run
```code
composer require "longlog/php-sdk" "~1.0"
```

# Usage
---

```php
<?php

/**
 * Your LongLog API client component example
 */
class MyLongLogComponent
{
    /**
     * Endpoint API url
     *
     * @var string For example: http://api.longlog.ru
     */
    public $endpointUrl;
    /**
     * Project secret token
     *
     * @var string 32 characters
     */
    public $projectToken;
    /**
     * LongLog API client instanse
     *
     * @var \longlog\Client
     */
    protected $client;

    /**
     * MyLongLogComponent constructor.
     *
     * @param string $endpointUrl  Endpoint API url
     * @param string $projectToken Project secret token
     */
    public function __construct($endpointUrl, $projectToken)
    {
        $this->endpointUrl = $endpointUrl;
        $this->projectToken = $projectToken;
    }

    /**
     * Get client instance (create if doesn't exists)
     *
     * @return \longlog\Client
     */
    public function getClient()
    {
        if (!$this->client) {
            // New client instance
            $this->client = new \longlog\Client($this->endpointUrl, $this->projectToken);
            $this->client->setTimeout(30);
        }

        return $this->client;
    }

    /**
     * Get new log object instance.
     * It helpful, new log instance already have link to client and can ->submit() using client
     *
     * @param string $jobName      Custom job name, for example: "CRON_SEND_EMAILS"
     * @param string|null $payload Log payload, it is simple string, for example: "userIds: [1, 2, 3]"
     *
     * @return \longlog\LongLogClientWrapper
     */
    public function getNewLog($jobName, $payload = null)
    {
        return new \longlog\LongLogClientWrapper($this->getClient(), $jobName, $payload);
    }
}


// Your API endpoint url
$endpointUrl = 'http://api.longlog.ru';
// Your project token
$projectToken = 'p8eGzXz5o4A2eulYhBvbrkghbAfirRwL';
// Initialize your component
$component = new MyLongLogComponent($endpointUrl, $projectToken);

// Running some long-timed operations
for ($i = 0; $i < 10; $i++) {
    $randomSleepSeconds = mt_rand(1, 3);
    $jobName = 'MY_JOB_NAME';
    $payload = "sleep seconds: $randomSleepSeconds";

    // ---- VARIANT 1 ---- (with client wrapper)
    // New LongLog instance
    $log = $component->getNewLog($jobName, $payload);
    // Remember the processing start time
    $log->start();

    // You job processing here
    sleep($randomSleepSeconds);

    // Calculate job processing time and submit log to API
    $log->finish()->submit();


    // ---- VARIANT 2 ---- (without client wrapper)
    // New LongLog instance
    $log = new \longlog\LongLog($jobName, $payload);
    // Remember processing start time
    $log->start();

    // You job processing here
    sleep($randomSleepSeconds);

    // Calculate job processing time and submit log to API
    $log->finish();
    $component->getClient()->submit($log);
}
```