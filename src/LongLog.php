<?php
/**
 * @copyright Copyright (c) 2017 Ivan Orlov
 * @license   https://github.com/demisang/longlog-php-sdk/blob/master/LICENSE
 * @link      https://github.com/demisang/longlog-php-sdk#readme
 */

namespace longlog;

use InvalidArgumentException;

/**
 * Log item
 */
class LongLog
{
    /**
     * Job name
     *
     * @var string
     */
    protected $jobName;
    /**
     * @var float
     */
    protected $duration;
    /**
     * Payload info
     *
     * @var string
     */
    protected $payload;
    /**
     * Custom log time (unix-timestamp)
     *
     * @var integer
     */
    protected $timestamp;

    /**
     * Long task start time
     *
     * @var float
     */
    protected $_timeStart;

    /**
     * LongLog constructor.
     *
     * @param string $jobName
     * @param mixed $payload
     */
    public function __construct($jobName, $payload = null)
    {
        $this->setJobName($jobName);
        $this->setPayload($payload);
    }

    /**
     * Save current time value before long job
     *
     * @return $this
     */
    public function start()
    {
        $this->_timeStart = microtime(true);

        return $this;
    }

    /**
     * Calculate long job duration
     *
     * @return $this
     */
    public function finish()
    {
        $this->setDuration(microtime(true) - $this->_timeStart);

        return $this;
    }

    /**
     * Set job name
     *
     * @param string $name
     *
     * @return $this
     */
    public function setJobName($name)
    {
        if (!is_string($name)) {
            throw new InvalidArgumentException('Job name must be a string');
        }

        $name = trim($name);
        $strlen = mb_strlen($name);

        if (!$strlen) {
            throw new InvalidArgumentException('Job name cannot be empty');
        } elseif ($strlen < 2 || $strlen > 255) {
            throw new InvalidArgumentException('Job name length must be in range 2-255 characters');
        }

        $this->jobName = $name;

        return $this;
    }

    /**
     * Get job name
     *
     * @return string
     */
    public function getJobName()
    {
        return $this->jobName;
    }

    /**
     * Set payload value
     *
     * @param mixed $data [1, 4, 5] or "1,4,5" or Object
     *
     * @return $this
     */
    public function setPayload($data)
    {
        if (!is_string($data)) {
            if (is_array($data)) {
                $data = json_encode($data, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
            } else {
                $data = serialize($data);
            }
        }

        $maxLength = 255;
        if (mb_strlen($data) > $maxLength) {
            // Truncate too long payload
            $data = mb_substr($data, 0, $maxLength - 3) . '...';
        }

        $this->payload = $data ? $data : null;

        return $this;
    }

    /**
     * Get payload value
     *
     * @return string
     */
    public function getPayload()
    {
        return $this->payload;
    }

    /**
     * Get duration value
     *
     * @return float
     */
    public function getDuration()
    {
        return $this->duration;
    }

    /**
     * Set custom duration value
     *
     * @param float $seconds min 0; max 999999.999
     *
     * @return $this
     */
    public function setDuration($seconds)
    {
        $seconds = round($seconds, 3);

        if ($seconds < 0 || $seconds > 999999.999) {
            throw new InvalidArgumentException('Duration seconds must be in range 0-999999.999');
        }

        $this->duration = $seconds;

        return $this;
    }

    /**
     * Get custom timestamp value
     *
     * @return int
     */
    public function getTimestamp()
    {
        return $this->timestamp;
    }

    /**
     * Set custom timestamp value
     *
     * @param int $timestamp
     */
    public function setTimestamp($timestamp)
    {
        $this->timestamp = $timestamp;
    }
}
