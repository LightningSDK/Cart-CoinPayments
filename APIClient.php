<?php

namespace lightningsdk\checkout_coinpayments;

use lightningsdk\core\Tools\Communicator\RestClient;
use lightningsdk\core\Tools\Configuration;

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
        $results = $this->get('result');
        $output = [];
        $currency = Configuration::get('modules.checkout.currency', 'USD');
        foreach ($results as $coin => $data) {
            if ($data['accepted'] == 1 || $coin == $currency) {
                $output[$coin] = $data;
            }
        }
        return $output;
    }

    public function getPaymentAddress($settings = []) {
        $params = [
            'cmd' => 'create_transaction',
            'amount' => $settings['amount'],
            'currency1' => !empty($settings['currency`']) ? $settings['currency'] : 'USD',
            'currency2' => !empty($settings['coin']) ? $settings['coin'] : 'BTC',
            'buyer_email' => $settings['email'],
            'buyer_name' => $settings['name'],
            'invoice_id' => $settings['order_id'],
        ];

        $config = Configuration::get('modules.coinpayments');
        if (!empty($config['ipn_url'])) {
            $params['ipn_url'] = $config['ipn_url'];
        }

        $this->setMultiple($params);
        $this->callPost();
        return $this->get('result');
    }
}
