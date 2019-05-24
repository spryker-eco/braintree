<?php

/**
 * MIT License
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace SprykerEco\Yves\Braintree\Model\Processor;

use Generated\Shared\Transfer\QuoteTransfer;

interface PaypalResponseProcessorInterface
{
    /**
     * @param array $payload
     *
     * @return \Generated\Shared\Transfer\QuoteTransfer
     */
    public function processSuccessResponse(array $payload): QuoteTransfer;
}
