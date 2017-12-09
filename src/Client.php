<?php
/**
 * @copyright Copyright (c) 2017 Ivan Orlov
 * @license   https://github.com/demisang/longlog-php-sdk/blob/master/LICENSE
 * @link      https://github.com/demisang/longlog-php-sdk#readme
 */

namespace longlog;

use InvalidArgumentException;

/**
 * LongLog API client
 */
class Client
{
    /**
     * Endpoint API url
     *
     * @var string
     */
    protected $endpointUrl;
    /**
     * Project secret token
     *
     * @var string
     */
    protected $projectToken;
    /**
     * Max connection timeout
     *
     * @var integer Timeout seconds
     */
    protected $timeout = 30;

    /**
     * Client constructor.
     *
     * @param string $endpointUrl
     * @param string $projectToken Secret token
     */
    public function __construct($endpointUrl, $projectToken)
    {
        $this->setEndpointUrl($endpointUrl);
        $this->setProjectToken($projectToken);
    }

    /**
     * Send log to API
     *
     * @param LongLog $longLog
     *
     * @return bool TRUE if log successfully saved
     */
    public function submit(LongLog $longLog)
    {
        // Request example: POST http://api.longlog.ru/project/log
        $curl = curl_init($this->getEndpointUrl() . '/project/log');
        curl_setopt($curl, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
        ]);
        curl_setopt($curl, CURLOPT_TIMEOUT, $this->getTimeout());
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);

        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($this->getRequestBody($longLog)));

        curl_exec($curl);
        $statusCode = (int)curl_getinfo($curl, CURLINFO_HTTP_CODE);
        curl_close($curl);

        return $statusCode === 201;
    }

    /**
     * Get request body
     *
     * @param LongLog $longLog
     *
     * @return array
     */
    protected function getRequestBody(LongLog $longLog)
    {
        $body = [
            'projectToken' => $this->getProjectToken(),
            'jobName' => $longLog->getJobName(),
            'duration' => $longLog->getDuration(),
            'timestamp' => $longLog->getTimestamp(),
        ];

        if ($longLog->getPayload()) {
            $body['payload'] = $longLog->getPayload();
        }

        return $body;
    }

    /**
     * Set endpoint url
     *
     * @param string $url
     *
     * @return $this
     */
    public function setEndpointUrl($url)
    {
        if (!$url) {
            throw new InvalidArgumentException('Endpoint URL required');
        }

        $this->endpointUrl = rtrim($url, '/');

        return $this;
    }

    /**
     * Get endpoint url value
     *
     * @return string
     */
    public function getEndpointUrl()
    {
        return $this->endpointUrl;
    }

    /**
     * Set project token
     *
     * @param string $token
     *
     * @return $this
     */
    public function setProjectToken($token)
    {
        if (!$token) {
            throw new InvalidArgumentException('Project token required');
        } elseif (strlen($token) !== 32) {
            throw new InvalidArgumentException('Project token must be 32 characters length');
        }

        $this->projectToken = $token;

        return $this;
    }

    /**
     * Get project token value
     *
     * @return string
     */
    public function getProjectToken()
    {
        return $this->projectToken;
    }

    /**
     * Set max timeout value
     *
     * @param integer $seconds
     *
     * @return $this
     */
    public function setTimeout($seconds)
    {
        if ($seconds <= 0) {
            throw new InvalidArgumentException('Timeout value should be a positive integer');
        }

        $this->timeout = $seconds;

        return $this;
    }

    /**
     * Get timeout value
     *
     * @return integer
     */
    public function getTimeout()
    {
        return $this->timeout;
    }
}
