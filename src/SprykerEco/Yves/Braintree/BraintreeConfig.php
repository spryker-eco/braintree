<?php

/**
 * MIT License
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace SprykerEco\Yves\Braintree;

use Spryker\Yves\Kernel\AbstractBundleConfig;
use SprykerEco\Shared\Braintree\BraintreeConstants;

class BraintreeConfig extends AbstractBundleConfig
{
    /**
     * @return int
     */
    public function getDefaultPaypalExpressShipmentMethodId(): int
    {
        return $this->get(BraintreeConstants::DEFAULT_PAYPAL_EXPRESS_SHIPMENT_METHOD_ID);
    }

    /**
     * @return string
     */
    public function getFakePaymentMethodNonce(): string
    {
        return $this->get(BraintreeConstants::FAKE_PAYMENT_METHOD_NONCE);
    }
}
