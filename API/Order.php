<?php

namespace Modules\CoinPayments\API;

use Exception;
use Lightning\Tools\Request;
use Lightning\View\API;
use Modules\CoinPayments\APIClient;
use Modules\Checkout\Model\Order as OrderModel;

class Order extends API {
    public function postCreateTransaction() {
        // Input validation
        $order = OrderModel::loadBySession();

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

        return [
            'coin' => $coin,
            'order_id' => $order->id,
        ] + $client->getPaymentAddress($options);
    }

    /**
     * @return array
     * @throws Exception
     */
    public function get() {
        $order = OrderModel::loadBySession(Request::get('order_id', Request::TYPE_INT), true);
        if (empty($order)) {
            throw new Exception('Invalid ');
        }

        return ['complete' => (bool) $order->locked];
    }
}
