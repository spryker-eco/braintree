<?php

/**
 * MIT License
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace SprykerEco\Shared\Braintree;

interface BraintreeConfig
{
    public const PROVIDER_NAME = 'Braintree';

    public const PAYMENT_METHOD_PAY_PAL = 'braintreePayPal';
    public const PAYMENT_METHOD_PAY_PAL_EXPRESS = 'braintreePayPalExpress';
    public const PAYMENT_METHOD_CREDIT_CARD = 'braintreeCreditCard';
}
