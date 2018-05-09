<?php

namespace Modules\CoinPayments\Connectors;

use Lightning\Tools\Template;
use Lightning\View\JS;
use Modules\Checkout\Handlers\Payment;
use Modules\Checkout\Model\Order;
use Modules\CoinPayments\APIClient;

class Checkout extends Payment {
    public function init() {
        // TODO: Cache this for 5 minutes;
        $client = new APIClient();
        $rates = $client->getCryptoOptions();

        JS::startup('lightning.modules.coinPayments.init();', '/js/Checkout.min.js');
        JS::set('modules.coinPayments.rates', $rates);
        Template::getInstance()->set('coinPayments', $this);
    }

    public function getDescription() {
        return 'Pay with Bitcoin, Ether, Dash, or one of many alt coins.';
    }

    public function getTitle() {
        return 'Crypto Currencies';
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
