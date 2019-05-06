<?php

/**
 * MIT License
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace SprykerEco\Service\Braintree;

use Spryker\Service\Kernel\AbstractServiceFactory;
use SprykerEco\Service\Braintree\Model\TokenGenerator\TokenGenerator;
use SprykerEco\Service\Braintree\Model\TokenGenerator\TokenGeneratorInterface;

/**
 * @method \SprykerEco\Service\Braintree\BraintreeConfig getConfig()
 */
class BraintreeServiceFactory extends AbstractServiceFactory
{
    /**
     * @return \SprykerEco\Service\Braintree\Model\TokenGenerator\TokenGeneratorInterface
     */
    public function createTokenGenerator(): TokenGeneratorInterface
    {
        return new TokenGenerator($this->getConfig());
    }
}
