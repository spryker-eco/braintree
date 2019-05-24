<?php

/**
 * MIT License
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace SprykerEco\Yves\Braintree\Dependency\Client;

interface BraintreeToPriceClientInterface
{
    /**
     * @return string
     */
    public function getNetPriceModeIdentifier();
}
