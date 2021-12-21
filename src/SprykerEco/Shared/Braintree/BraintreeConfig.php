<?php

/**
 * MIT License
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace SprykerEco\Shared\Braintree;

interface BraintreeConfig
{
    /**
     * @var string
     */
    public const PROVIDER_NAME = 'Braintree';

    /**
     * @var string
     */
    public const PAYMENT_METHOD_PAY_PAL = 'braintreePayPal';

    /**
     * @var string
     */
    public const PAYMENT_METHOD_PAY_PAL_EXPRESS = 'braintreePayPalExpress';

    /**
     * @var string
     */
    public const PAYMENT_METHOD_CREDIT_CARD = 'braintreeCreditCard';
}
