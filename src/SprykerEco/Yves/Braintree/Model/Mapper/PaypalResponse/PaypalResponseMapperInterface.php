<?php

/**
 * MIT License
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace SprykerEco\Yves\Braintree\Model\Mapper\PaypalResponse;

use Generated\Shared\Transfer\PaypalExpressSuccessResponseTransfer;
use Generated\Shared\Transfer\QuoteTransfer;

interface PaypalResponseMapperInterface
{
    /**
     * @param array $payload
     *
     * @return \Generated\Shared\Transfer\PaypalExpressSuccessResponseTransfer
     */
    public function mapSuccessResponseToPaypalExpressSuccessResponseTransfer(array $payload): PaypalExpressSuccessResponseTransfer;

    /**
     * @param \Generated\Shared\Transfer\PaypalExpressSuccessResponseTransfer $paypalExpressSuccessResponseTransfer
     * @param \Generated\Shared\Transfer\QuoteTransfer $quoteTransfer
     *
     * @return \Generated\Shared\Transfer\QuoteTransfer
     */
    public function mapPaypalExpressSuccessResponseTransferToQuoteTransfer(
        PaypalExpressSuccessResponseTransfer $paypalExpressSuccessResponseTransfer,
        QuoteTransfer $quoteTransfer
    ): QuoteTransfer;
}
