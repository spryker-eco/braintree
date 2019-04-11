<?php

/**
 * MIT License
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace SprykerEco\Service\Braintree;

use Spryker\Service\Kernel\AbstractService;

/**
 * @method \SprykerEco\Service\Braintree\BraintreeServiceFactory getFactory()
 */
class BraintreeService extends AbstractService implements BraintreeServiceInterface
{
    /**
     * @return string
     */
    public function generateToken(): string
    {
        return $this->getFactory()
            ->createTokenGenerator()
            ->generateToken();
    }
}
