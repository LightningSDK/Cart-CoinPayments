<?php

namespace Modules\CoinPayments\API;

use Exception;
use Lightning\Tools\Configuration;
use Lightning\Tools\Logger;
use Lightning\Tools\Mailer;
use Lightning\Tools\Output;
use Lightning\Tools\Request;
use Lightning\View\API;
use Modules\Checkout\Model\Payment;
use Modules\CoinPayments\Model\Transaction;

class Notifications extends API {
    public function post() {

        // Authenticate the transaction
        // Fill these in with the information from your CoinPayments.net account.
        $cp_merchant_id = Configuration::get('modules.coinpayments.merchantId');
        $cp_ipn_secret = Configuration::get('modules.coinpayments.ipnSecret');

        if (!isset($_POST['ipn_mode']) || $_POST['ipn_mode'] != 'hmac') {
            Output::error('IPN Mode is not HMAC');
        }

        if (!isset($_SERVER['HTTP_HMAC']) || empty($_SERVER['HTTP_HMAC'])) {
            Output::error('No HMAC signature sent.');
        }

        $request = file_get_contents('php://input');
        if ($request === FALSE || empty($request)) {
            Output::error('Error reading POST data');
        }

        if (!isset($_POST['merchant']) || $_POST['merchant'] != trim($cp_merchant_id)) {
            Output::error('No or incorrect Merchant ID passed');
        }

        $hmac = hash_hmac("sha512", $request, trim($cp_ipn_secret));
        if (!hash_equals($hmac, $_SERVER['HTTP_HMAC'])) {
            //if ($hmac != $_SERVER['HTTP_HMAC']) { <-- Use this if you are running a version of PHP below 5.6.0 without the hash_equals function
            Output::error('HMAC signature does not match');
        }

        // HMAC Signature verified at this point, load some variables.

        $txn_id = Request::post('txn_id');
//        $item_name = $_POST['item_name'];
//        $item_number = $_POST['item_number'];

        // The order currency and value
        $amount1 = floatval($_POST['amount1']);
        $currency1 = $_POST['currency1'];

        // The coin type and value
        $amount2 = floatval($_POST['amount2']);
        $currency2 = $_POST['currency2'];

        $status = intval($_POST['status']);
        $status_text = $_POST['status_text'];

        if (count(Payment::loadByTransactionId($txn_id)) > 0) {
            // This transaction has already been received.
            return Output::SUCCESS;
        }

        if ($status >= 100 || $status == 2) {
            // payment is complete or queued for nightly payout, success
        } else if ($status < 0) {
            //payment error, this is usually final but payments will sometimes be reopened if there was no exchange rate conversion or with seller consent
            return Output::SUCCESS;
        } else {
            //payment is pending, you can optionally add a note to the order page
            return Output::SUCCESS;
        }

        // Load the transaction to get the order ID.
        $transaction = Transaction::loadByTransaction($txn_id);
        if (empty($transaction)) {
            Logger::error("[coinpayments] Received IPN with txn_id {$txn_id} that could not be found.");
            throw new Exception('Transaction not found');
        }

        // Mark the order as complete
        $order = \Modules\Checkout\Model\Order::loadByID($transaction->order_id);
        if (empty($order)) {
            Logger::error("[coinpayments] Received IPN with txn_id {$txn_id} and order number {$transaction->order_id}, but the order was not found.");
            throw new Exception('Order not found');
        }
        $order->addPayment($amount2, $currency2, $txn_id);

        $order->sendNotifications();

        if ($order->getTotal() != $amount1) {
            // Send alternate notification
            $mailer = new Mailer();
            $mailer->to(Configuration::get('contact.to')[0]);
            $mailer->subject('An order payment was received for the wrong amount.');
            $mailer->message('A payment of ' . $amount1 . ' ' . $currency1 . ' was received for the order ' . $transaction->order_id . ', but ' . $order->getTotal() . ' was expected');
            $mailer->send();
        }

        return Output::SUCCESS;
    }
}
