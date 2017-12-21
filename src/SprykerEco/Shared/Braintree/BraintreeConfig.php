<?php

/**
 * MIT License
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerEco\Shared\Braintree;

interface BraintreeConfig
{
    const PROVIDER_NAME = 'Braintree';

    const PAYMENT_METHOD_PAY_PAL = 'braintreePayPal';
    const PAYMENT_METHOD_CREDIT_CARD = 'braintreeCreditCard';
}
