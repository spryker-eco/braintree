<?php

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
     * @return TokenGeneratorInterface
     */
    public function createTokenGenerator(): TokenGeneratorInterface
    {
        return new TokenGenerator($this->getConfig());
    }
}