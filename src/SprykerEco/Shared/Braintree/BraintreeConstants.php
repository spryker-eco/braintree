<?php

/**
 * MIT License
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace SprykerEco\Shared\Braintree;

/**
 * Declares global environment configuration keys. Do not use it for other class constants.
 */
interface BraintreeConstants
{
    public const ENVIRONMENT = 'BRAINTREE:ENVIRONMENT';

    public const MERCHANT_ID = 'BRAINTREE:MERCHANT_ID';
    public const PUBLIC_KEY = 'BRAINTREE:PUBLIC_KEY';
    public const PRIVATE_KEY = 'BRAINTREE:PRIVATE_KEY';

    public const ACCOUNT_ID = 'BRAINTREE:ACCOUNT_ID';
    public const ACCOUNT_UNIQUE_IDENTIFIER = 'BRAINTREE:ACCOUNT_UNIQUE_IDENTIFIER';

    public const IS_3D_SECURE = 'BRAINTREE:IS_3D_SECURE';
    public const IS_VAULTED = 'BRAINTREE:IS_VAULTED';

    public const DEFAULT_PAYPAL_EXPRESS_SHIPMENT_METHOD_ID = 'BRAINTREE:DEFAULT_PAYPAL_EXPRESS_SHIPMENT_METHOD_ID';
    public const FAKE_PAYMENT_METHOD_NONCE = 'BRAINTREE:FAKE_PAYMENT_METHOD_NONCE';
    public const FAKE_CLIENT_TOKEN = 'BRAINTREE:FAKE_CLIENT_TOKEN';
}
