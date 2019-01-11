<?php

/**
 * MIT License
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace SprykerEco\Zed\Braintree;

use Spryker\Zed\Kernel\AbstractBundleConfig;
use SprykerEco\Shared\Braintree\BraintreeConstants;

class BraintreeConfig extends AbstractBundleConfig
{
    /**
     * @return string
     */
    public function getPublicKey()
    {
        return $this->get(BraintreeConstants::PUBLIC_KEY);
    }

    /**
     * @return string
     */
    public function getPrivateKey()
    {
        return $this->get(BraintreeConstants::PRIVATE_KEY);
    }

    /**
     * @return string
     */
    public function getMerchantId()
    {
        return $this->get(BraintreeConstants::MERCHANT_ID);
    }

    /**
     * @return string
     */
    public function getEnvironment()
    {
        return $this->get(BraintreeConstants::ENVIRONMENT);
    }

    /**
     * @return bool
     */
    public function getIsVaulted()
    {
        return $this->get(BraintreeConstants::IS_VAULTED, false);
    }

    /**
     * @return bool
     */
    public function getIs3DSecure()
    {
        return $this->get(BraintreeConstants::IS_3D_SECURE, false);
    }

    /**
     * @return string
     */
    final public function getChannel()
    {
        return 'Spryker_BT_DE';
    }
}
