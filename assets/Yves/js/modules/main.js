/**
 * MIT License
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

'use strict';

var $ = require('jquery');
var paymentMethod = require('./payment-method');

$(document).ready(function() {
    if (window.braintree == null) {
        window.braintree = true;
        paymentMethod.init({
            formSelector: '#payment-form',
            paymentMethodSelector: 'input[id^="paymentForm_paymentSelection"][type="radio"]',
            currentPaymentMethodSelector: 'input[id^="paymentForm_paymentSelection"][type="radio"]:checked',
            nonceInputName: 'payment_method_nonce'
        });
    }
});
