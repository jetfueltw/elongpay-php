<?php

namespace Jetfuel\Elongpay;

use Jetfuel\Elongpay\Traits\ResultParser;


//只適用網銀
class TradeQuery extends Payment
{
    use ResultParser;

    /**
     * TradeQuery constructor.
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
     * Find Order by trade number.
     *
     * @param string $tradeNo
     * @return array|null
     */
    public function find($tradeNo)
    {
        $payload = $this->signPayload([
            'out_trade_no'     => $tradeNo,
        ]);

        $order = $this->parseResponse($this->httpClient->post('trade/get', $payload));
        
        if (!isset($order['status'])) {
              return null;
        }

        return $order;
    }

    /**
     * Is order already paid.
     *
     * @param string $tradeNo
     * @return bool
     */
    public function isPaid($tradeNo)
    {
        $order = $this->find($tradeNo);

        if ($order === null || $order['status'] !== '1001') {
            return false;
        }

        return true;
    }
}
