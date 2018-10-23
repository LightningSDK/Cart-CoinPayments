<div class="row" id="currency-selector">
    <div class="small-12 medium-8 medium-offset-2 column">
        <h2>Which crypto currency would you like to use?</h2>
        <select name="cryptoCurrency" class="crypto-selector"></select>
        <p class="text-center">
            <img src="/images/checkout/logos/coinpayments.png" style="height: 50px;" />
        </p>
        <div class="paymentQRCode text-center" style="display: none">
            Please send <span class="coinAmount"></span> <span class="coin"></span> to <span class="address"></span>
            <br><br>
            <img>
            <br><br>
            Please be patient, as some coins take longer to confirm than others. Once your payment has been received, you will receive an email confirmation.
        </div>
    </div>
</div>
<div class="row" id="order-complete-message" style="display:none">
    <div class="column">
        <br><br><br>
        <div class="messenger message">
            Your payment has been received. Your order #<span id="success_order_number"></span> is being processed.
        </div>
        <br><br><br>
    </div>
</div>
<br><br><br>
<br><br><br>
