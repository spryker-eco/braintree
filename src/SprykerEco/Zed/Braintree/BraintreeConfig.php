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
     * @api
     *
     * @return string
     */
    public function getPublicKey()
    {
        return $this->get(BraintreeConstants::PUBLIC_KEY);
    }

    /**
     * @api
     *
     * @return string
     */
    public function getPrivateKey()
    {
        return $this->get(BraintreeConstants::PRIVATE_KEY);
    }

    /**
     * @api
     *
     * @return string
     */
    public function getMerchantId()
    {
        return $this->get(BraintreeConstants::MERCHANT_ID);
    }

    /**
     * @api
     *
     * @return string
     */
    public function getEnvironment()
    {
        return $this->get(BraintreeConstants::ENVIRONMENT);
    }

    /**
     * @api
     *
     * @return bool
     */
    public function getIsVaulted()
    {
        return $this->get(BraintreeConstants::IS_VAULTED, false);
    }

    /**
     * @api
     *
     * @return bool
     */
    public function getIs3DSecure()
    {
        return $this->get(BraintreeConstants::IS_3D_SECURE, false);
    }

    /**
     * @api
     *
     * @return string
     */
    final public function getChannel()
    {
        return 'Spryker_BT_DE';
    }
}
