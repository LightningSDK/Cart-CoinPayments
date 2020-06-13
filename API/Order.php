<?php

namespace lightningsdk\checkout_coinpayments\API;

use Exception;
use lightningsdk\core\Tools\Request;
use lightningsdk\core\View\API;
use lightningsdk\checkout_coinpayments\APIClient;
use lightningsdk\checkout\Model\Order as OrderModel;
use lightningsdk\checkout_coinpayments\Model\Transaction;

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

        $payment_info = $client->getPaymentAddress($options);

        Transaction::create([
            'order_id' => $order->id,
            'transaction_id' => $payment_info['txn_id'],
        ]);

        return [
            'coin' => $coin,
            'order_id' => $order->id,
        ] + $payment_info;
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
