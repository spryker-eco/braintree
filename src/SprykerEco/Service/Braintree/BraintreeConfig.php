<?php

/**
 * MIT License
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace SprykerEco\Service\Braintree;

use Spryker\Service\Kernel\AbstractBundleConfig;
use SprykerEco\Shared\Braintree\BraintreeConstants;

class BraintreeConfig extends AbstractBundleConfig
{
    /**
     * @return string
     */
    public function getEnvironment(): string
    {
        return $this->get(BraintreeConstants::ENVIRONMENT);
    }

    /**
     * @return string
     */
    public function getPublicKey(): string
    {
        return $this->get(BraintreeConstants::PUBLIC_KEY);
    }

    /**
     * @return string
     */
    public function getPrivateKey(): string
    {
        return $this->get(BraintreeConstants::PRIVATE_KEY);
    }

    /**
     * @return string
     */
    public function getMerchantId(): string
    {
        return $this->get(BraintreeConstants::MERCHANT_ID);
    }
}
