<?php

namespace Modules\CoinPayments\API;

use Lightning\Tools\Request;
use Lightning\View\API;
use Modules\CoinPayments\APIClient;

class Order extends API {
    public function postCreateTransaction() {
        // Input validation
        $order = \Modules\Checkout\Model\Order::loadBySession();

        $client = new APIClient();

        // Prepare the order:
        $coin = Request::get('coin');
        $options = [
            'amount' => $order->getTotal(),
            'currency' => 'USD',
            'coin' => $coin,
            'email' => $order->getUser()->email,
            'name' => $order->getUser()->fullName(),
            'order_id' => $order->id,
        ];

        return ['coin' => $coin] + $client->getPaymentAddress($options);
    }
}
