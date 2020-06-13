<?php

namespace lightningsdk\checkout_coinpayments\Model;

use lightningsdk\core\Model\Object;
use lightningsdk\core\Tools\Database;

class Transaction extends Object {
    const TABLE = 'checkout_coinpayments';
    const PRIMARY_KEY = 'coinpayments_id';

    public static function loadByTransaction($txn_id) {
        if ($data = Database::getInstance()->selectRow(static::TABLE, ['transaction_id' => $txn_id])) {
            return new static($data);
        } else {
            return null;
        }
    }
}
