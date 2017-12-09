<?php
/**
 * @copyright Copyright (c) 2017 Ivan Orlov
 * @license   https://github.com/demisang/longlog-php-sdk/blob/master/LICENSE
 * @link      https://github.com/demisang/longlog-php-sdk#readme
 */

namespace longlog;

/**
 * LongLog item with client wrapper
 */
class LongLogClientWrapper extends \longlog\LongLog
{
    /**
     * API client instance
     *
     * @var \longlog\Client
     */
    protected $client;

    /**
     * LongLog constructor.
     *
     * @param \longlog\Client $client
     * @param string $jobName
     * @param mixed $payload
     */
    public function __construct(\longlog\Client $client, $jobName, $payload = null)
    {
        $this->client = $client;

        parent::__construct($jobName, $payload);
    }

    /**
     * Create new LongLog object with client variable
     *
     * @param \longlog\Client $client
     * @param string $jobName
     * @param mixed $payload
     *
     * @return static
     */
    public static function create($client, $jobName, $payload = null)
    {
        $longLog = new self($client, $jobName, $payload);

        return $longLog;
    }

    /**
     * Send log to API
     *
     * @return bool
     */
    public function submit()
    {
        return $this->client->submit($this);
    }
}
