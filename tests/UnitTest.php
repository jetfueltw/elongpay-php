<?php

namespace Test;

use Faker\Factory;
use Jetfuel\Elongpay\BankPayment;
use Jetfuel\Elongpay\Constants\Bank;
use Jetfuel\Elongpay\Constants\Channel;
use Jetfuel\Elongpay\DigitalPayment;
use Jetfuel\Elongpay\TradeQuery;
use Jetfuel\Elongpay\Traits\NotifyWebhook;
use Jetfuel\Elongpay\BalanceQuery;
use PHPUnit\Framework\TestCase;

class UnitTest extends TestCase
{
    private $orgId;
    private $merchantId;
    private $secretKey;

    public function __construct($name = null, array $data = [], $dataName = '')
    {
        parent::__construct($name, $data, $dataName);

        $this->merchantId = getenv('MERCHANT_ID');
        $this->secretKey = getenv('SECRET_KEY');
    }

    public function testDigitalPaymentOrder()
    {
        $faker = Factory::create();
        $tradeNo = date('YmdHis').rand(1000, 9999);
        $channel = Channel::WECHAT;
        $amount = 10;
        $notifyUrl = 'http://a.a.com';
        //$returnUrl = 'http://a.a.com';

        $payment = new DigitalPayment($this->merchantId, $this->secretKey);
        $result = $payment->order($tradeNo, $channel, $amount, $notifyUrl);
        var_dump($result);
        $this->assertArrayHasKey('content',$result);
        
        return $tradeNo;
    }

    /**
     * @depends testDigitalPaymentOrder
     *
     * @param $tradeNo
     */
    public function testDigitalPaymentOrderFind($tradeNo)
    {
        $tradeQuery = new TradeQuery($this->merchantId, $this->secretKey);
        $result = $tradeQuery->find($tradeNo);
        
        $this->assertEquals('1001', $result['status']);
    }

    /**
     * @depends testDigitalPaymentOrder
     *
     * @param $tradeNo
     */
    public function testDigitalPaymentOrderIsPaid($tradeNo)
    {
        $tradeQuery = new TradeQuery($this->merchantId, $this->secretKey);
        $result = $tradeQuery->isPaid($tradeNo);

        $this->assertFalse($result);
    }

    // public function testBankPaymentOrder()
    // {
    //     $faker = Factory::create();
    //     $tradeNo = date('YmdHis').rand(1000, 9999);
    //     $bank = Bank::CMBC;
    //     $amount = 10;
    //     $returnUrl = 'http://www.yahoo.com';//$faker->url;
    //     $notifyUrl = 'http://www.yahoo.com';//'$faker->url;

    //     $payment = new BankPayment($this->merchantId, $this->secretKey);
    //     $result = $payment->order($tradeNo, $bank, $amount, $notifyUrl);
    //     var_dump($result);

    //     $this->assertContains('http', $result, '', true);

    //     return $tradeNo;
    // }

    /**
     * @dependss testBankPaymentOrder
     *
     * @param $tradeNo
     */
    // public function testBankPaymentOrderFind($tradeNo)
    // {
    //     $tradeQuery = new TradeQuery($this->merchantId, $this->secretKey);
    //     $result = $tradeQuery->find($tradeNo);
        
    //     $this->assertEquals('0000', $result['realpay_trade_query_response']['code']);
    // }

    /**
     * @dependss testBankPaymentOrder
     *
     * @param $tradeNo
     */
    // public function testBankPaymentOrderIsPaid($tradeNo)
    // {
    //     $tradeQuery = new TradeQuery($this->merchantId, $this->secretKey);
    //     $result = $tradeQuery->isPaid($tradeNo);

    //     $this->assertFalse($result);
    // }

    // public function testTradeQueryFindOrderNotExist()
    // {
    //     $faker = Factory::create();
    //     $tradeNo = substr($faker->uuid,0,20);

    //     $tradeQuery = new TradeQuery($this->merchantId, $this->secretKey);
    //     $result = $tradeQuery->find($tradeNo);

    //     $this->assertNull($result);
    // }

    // public function testTradeQueryIsPaidOrderNotExist()
    // {
    //     $faker = Factory::create();
    //     $tradeNo = substr($faker->uuid,0,20);

    //     $tradeQuery = new TradeQuery($this->merchantId, $this->secretKey);
    //     $result = $tradeQuery->isPaid($tradeNo);

    //     $this->assertFalse($result);
    // }

    public function testNotifyWebhookVerifyNotifyPayload()
    {
        $mock = $this->getMockForTrait(NotifyWebhook::class);

        $payload = [
            'version'        => '1.0.0',
            'trade_no'       => '2018091015000011',
            'out_trade_no'   => '2018091015000011',
            'total_amount'   => '10.00',
            'goods_name'     => 'GOODS_NAME',
            'remarks'        => 'remarks',
            'status'         => '1000',
            'pay_time'       => '2018-09-10 15:10:10',
            'sign'           => '61E78FAC3B2273E1F35703432321F913',
        ];
        
        $this->assertTrue($mock->verifyNotifyPayload($payload, $this->secretKey));
    }

    public function testNotifyWebhookParseNotifyPayload()
    {
        $mock = $this->getMockForTrait(NotifyWebhook::class);

        $payload = [
            'version'        => '1.0.0',
            'trade_no'       => '2018091015000011',
            'out_trade_no'   => '2018091015000011',
            'total_amount'   => '10.00',
            'goods_name'     => 'GOODS_NAME',
            'remarks'        => 'remarks',
            'status'         => '1000',
            'pay_time'       => '2018-09-10 15:10:10',
            'sign'           => '61E78FAC3B2273E1F35703432321F913',
        ];

         $this->assertEquals([
            'version'        => '1.0.0',
            'trade_no'       => '2018091015000011',
            'out_trade_no'   => '2018091015000011',
            'total_amount'   => '10.00',
            'goods_name'     => 'GOODS_NAME',
            'remarks'        => 'remarks',
            'status'         => '1000',
            'pay_time'       => '2018-09-10 15:10:10',
            'sign'           => '61E78FAC3B2273E1F35703432321F913',
         ], $mock->parseNotifyPayload($payload, $this->secretKey));
    }

    public function testNotifyWebhookSuccessNotifyResponse()
    {
        $mock = $this->getMockForTrait(NotifyWebhook::class);

        $this->assertEquals('success', $mock->successNotifyResponse());
    }

}
