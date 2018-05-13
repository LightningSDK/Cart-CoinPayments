(function() {
    if (lightning.modules.coinPayments) {
        return;
    }
    var self = lightning.modules.coinPayments = {
        meta: {},
        amount: 0,
        cryptoSelector: null,
        polling: false,

        init: function () {
            self.cryptoSelector = $('.crypto-selector');

            if (self.cryptoSelector.length > 0) {
                var cartTotal = lightning.get('modules.checkout.total');
                var cartCurrency = lightning.get('modules.checkout.currency');

                var cartBTC = 0;

                // Load the rates data:
                var rates = lightning.get('modules.coinPayments.rates');
                self.shortRates = {};
                for (var i in rates) {
                    if (i === cartCurrency) {
                        cartBTC = rates[i].rate_btc;
                    }
                    else if (rates[i].is_fiat === 0) {
                        self.shortRates[i] = {
                            'rate_btc': rates[i].rate_btc,
                            'name': rates[i].name
                        };
                    }
                }

                // Do the conversions from cart currency:
                if (cartBTC === 0) {
                    console.error('No currency conversion found');
                    return;
                }

                // Populate the currency selector
                self.cryptoSelector.empty().append('<option>Select One</option>');
                for (i in self.shortRates) {
                    self.shortRates[i].total = cartTotal * cartBTC / self.shortRates[i].rate_btc;
                    self.cryptoSelector.append('<option value="' + i + '">' + self.shortRates[i].name + '</option>');
                }

                // Set the change event
                self.cryptoSelector.on('change', self.buildTransactionRequest);
            }
        },

        buildTransactionRequest: function () {
            $.ajax({
                url: '/api/coinpayments/order',
                method: 'POST',
                data: {
                    action: 'create-transaction',
                    coin: self.cryptoSelector.val()
                },
                success: function(data) {
                    self.orderId = data.order_id;
                    var container = $('.paymentQRCode').show();
                    container.find('img').prop('src', data['qrcode_url']);
                    container.find('.coin').text(data['coin']);
                    container.find('.coinAmount').text(data['amount']);
                    container.find('.address').text(data['address']);
                    self.beginPolling();
                }
            });
        },

        beginPolling: function() {
            if (self.polling) {
                return;
            }

            self.polling = true;
            self.resetPoll();
        },

        resetPoll: function() {
            $.ajax({
                url: '/api/coinpayments/order?order_id=' + self.orderId,
                success: function(data) {
                    if (data.complete === true) {
                        document.location = '/store/checkout?page=confirmation';
                    } else {
                        setTimeout(self.resetPoll, 1000);
                    }
                }
            });
        }
    };
}());
