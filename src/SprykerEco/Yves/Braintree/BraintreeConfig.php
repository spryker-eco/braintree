<?php

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
}