<?php

/**
 * MIT License
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerEco\Shared\Braintree;

interface BraintreeConstants
{
    const ENVIRONMENT = 'BRAINTREE:ENVIRONMENT';

    const MERCHANT_ID = 'BRAINTREE:MERCHANT_ID';
    const PUBLIC_KEY = 'BRAINTREE:PUBLIC_KEY';
    const PRIVATE_KEY = 'BRAINTREE:PRIVATE_KEY';

    const ACCOUNT_ID = 'BRAINTREE:ACCOUNT_ID';
    const ACCOUNT_UNIQUE_IDENTIFIER = 'BRAINTREE:ACCOUNT_UNIQUE_IDENTIFIER';

    const IS_3D_SECURE = 'BRAINTREE:IS_3D_SECURE';
    const IS_VAULTED = 'BRAINTREE:IS_VAULTED';
}
