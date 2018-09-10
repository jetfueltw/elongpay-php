<?php

namespace Jetfuel\Elongpay;

use Jetfuel\Elongpay\HttpClient\GuzzleHttpClient;

class Payment
{
   const BASE_API_URL = 'http://npay.elongpay.com/';
    const TIME_ZONE      = 'Asia/Shanghai';
    const TIME_FORMAT    = 'Y-m-d H:i:s';

      /**
     * @var string
     */
    protected $orgId;

    /**
     * @var string
     */
    protected $merchantId;

    /**
     * @var string
     */
    protected $secretKey;

    /**
     * @var string
     */
    protected $baseApiUrl;

    /**
     * @var \Jetfuel\Elongpay\HttpClient\HttpClientInterface
     */
    protected $httpClient;

    /**
     * Payment constructor.
     *
     * @param string $orgId
     * @param string $merchantId
     * @param string $secretKey
     * @param null|string $baseApiUrl
     */
    protected function __construct($merchantId, $secretKey, $baseApiUrl = null)
    {
        //$this->orgId = $orgId;
        $this->merchantId = $merchantId;
        $this->secretKey = $secretKey;
        $this->baseApiUrl = $baseApiUrl === null ? self::BASE_API_URL : $baseApiUrl;

        $this->httpClient = new GuzzleHttpClient($this->baseApiUrl);
    }

    /**
     * Sign request payload.
     *
     * @param array $payload
     * @return array
     */
    protected function signPayload(array $payload)
    {
        $payload['appid'] = $this->merchantId;
        $payload['version'] = '1.0.0';
        $payload['request_time'] = $this->getCurrentTime();

        $payload['sign'] = Signature::generate($payload, $this->secretKey);

        return $payload;
    }

    /**
     * Get current time.
     *
     * @return string
     */
    protected function getCurrentTime()
    {
        return (new \DateTime('now', new \DateTimeZone(self::TIME_ZONE)))->format(self::TIME_FORMAT);
    }

}
