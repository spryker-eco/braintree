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
    /**
     * @var string
     */
    public const ENVIRONMENT = 'BRAINTREE:ENVIRONMENT';

    /**
     * @var string
     */
    public const MERCHANT_ID = 'BRAINTREE:MERCHANT_ID';

    /**
     * @var string
     */
    public const PUBLIC_KEY = 'BRAINTREE:PUBLIC_KEY';

    /**
     * @var string
     */
    public const PRIVATE_KEY = 'BRAINTREE:PRIVATE_KEY';

    /**
     * @var string
     */
    public const ACCOUNT_ID = 'BRAINTREE:ACCOUNT_ID';

    /**
     * @var string
     */
    public const ACCOUNT_UNIQUE_IDENTIFIER = 'BRAINTREE:ACCOUNT_UNIQUE_IDENTIFIER';

    /**
     * @var string
     */
    public const IS_3D_SECURE = 'BRAINTREE:IS_3D_SECURE';

    /**
     * @var string
     */
    public const IS_VAULTED = 'BRAINTREE:IS_VAULTED';

    /**
     * @var string
     */
    public const DEFAULT_PAYPAL_EXPRESS_SHIPMENT_METHOD_ID = 'BRAINTREE:DEFAULT_PAYPAL_EXPRESS_SHIPMENT_METHOD_ID';

    /**
     * @var string
     */
    public const FAKE_PAYMENT_METHOD_NONCE = 'BRAINTREE:FAKE_PAYMENT_METHOD_NONCE';

    /**
     * @var string
     */
    public const FAKE_CLIENT_TOKEN = 'BRAINTREE:FAKE_CLIENT_TOKEN';
}
