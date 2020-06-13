<?php

namespace lightningsdk\checkout_coinpayments\Connectors;

use lightningsdk\core\Tools\Configuration;
use lightningsdk\core\Tools\Template;
use lightningsdk\core\View\JS;
use lightningsdk\checkout\Handlers\Payment;
use lightningsdk\checkout\Model\Order;
use lightningsdk\checkout_coinpayments\APIClient;

class Checkout extends Payment {
    public function init() {
        // TODO: Cache this for 5 minutes;
        $client = new APIClient();
        $rates = $client->getCryptoOptions();

        JS::startup('lightning.modules.coinPayments.init();', '/js/Checkout.min.js');
        JS::set('modules.coinPayments.rates', $rates);
        Template::getInstance()->set('coinPayments', $this);
    }

    public function isConfigured() {
        $config = Configuration::get('modules.coinpayments');
        return !empty($config['public']);
    }

    public function getDescription() {
        return 'Pay with Bitcoin, Ether, Dash, or one of many alt coins.';
    }

    public function getTitle() {
        return 'Crypto Currencies';
    }

    public function getLogo() {
        return '/images/checkout/logos/coinpayments.png';
    }

    public function getPage(Order $cart) {
        return ['process', 'CoinPayments'];
    }

    public function prepare(Order $order) {
        $template = Template::getInstance();
        $paymentAddress = (new APIClient())->getPaymentAddress([
            'email' => $order->getUser()->email,
            'currency1' => 'USD',
            'currency2' => 'BCT',
        ]);
    }
}
