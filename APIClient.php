<?php

namespace Modules\CoinPayments;

use Lightning\Tools\Communicator\RestClient;
use Lightning\Tools\Configuration;

class APIClient extends RestClient {

    public function __construct() {
        parent::__construct('https://www.coinpayments.net/api.php');
    }

    public function callPost($path = '') {
        $this->set('key', Configuration::get('modules.coinpayments.public'));
        $this->set('version', 1);
        parent::callPost($path);
    }

    protected function signBodyContents(&$content) {
        $privateKey = Configuration::get('modules.coinpayments.private');
        $hmac = hash_hmac('sha512', $content, $privateKey);

        $this->setHeader('HMAC', $hmac);
    }

    public function getCryptoOptions() {
        $params = [
            'cmd' => 'rates',
            'accepted' => 1,
        ];

        $this->setMultiple($params);
        $this->callPost();
        return $this->get('result');
    }

    public function getPaymentAddress($settings = []) {
        $params = [
            'cmd' => 'create_transaction',
            'amount' => $settings['amount'],
            'currency1' => !empty($settings['currency`']) ? $settings['currency'] : 'USD',
            'currency2' => !empty($settings['coin']) ? $settings['coin'] : 'BTC',
            'buyer_email' => $settings['email'],
            'buyer_name' => $settings['name'],
            'invoice_ud' => $settings['order_id'],
        ];

        $this->setMultiple($params);
        $this->callPost();
        return $this->get('result');
    }
}
