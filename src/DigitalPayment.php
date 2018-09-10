<?php

namespace Jetfuel\Elongpay;

use Jetfuel\Elongpay\Traits\ResultParser;
use Jetfuel\Elongpay\Constants\Channel;

class DigitalPayment extends Payment
{
    use ResultParser;

    /**
     * DigitalPayment constructor.
     *
     * @param string $merchantId
     * @param string $secretKey
     * @param null|string $baseApiUrl
     */
    public function __construct($merchantId, $secretKey, $baseApiUrl = null)
    {
        parent::__construct($merchantId, $secretKey, $baseApiUrl);
    }

    /**
     * Create digital payment order.
     *
     * @param string $tradeNo
     * @param string $channel
     * @param float $amount
     * @param string $notifyUrl
     * @param string $returnUrl
     * @return array
     */
    public function order($tradeNo, $channel, $amount, $notifyUrl)
    {
        
        $payload = $this->signPayload([
            'out_trade_no'        => $tradeNo,
            'goods_name'          => 'GOODS_NAME',
            'total_amount'        => $amount,
            'channel_code'        => $channel,
            'notify_url'          => $notifyUrl,
            'device'              => '1',
        ]);
        return $this->parseResponse($this->httpClient->post('/trade/create', $payload));
    }
}
