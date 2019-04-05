<?php

namespace SprykerEco\Yves\Braintree\Model\Mapper\PaypalResponse;

use Generated\Shared\Transfer\PaypalExpressSuccessResponseTransfer;
use Generated\Shared\Transfer\QuoteTransfer;

interface PaypalResponseMapperInterface
{
    /**
     * @param array $payload
     *
     * @return PaypalExpressSuccessResponseTransfer
     */
    public function mapSuccessResponseToPaypalExpressSuccessResponseTransfer(array $payload): PaypalExpressSuccessResponseTransfer;

    /**
     * @param PaypalExpressSuccessResponseTransfer $paypalExpressSuccessResponseTransfer
     * @param QuoteTransfer $quoteTransfer
     *
     * @return QuoteTransfer
     */
    public function mapPaypalExpressSuccessResponseTransferToQuoteTransfer(
        PaypalExpressSuccessResponseTransfer $paypalExpressSuccessResponseTransfer,
        QuoteTransfer $quoteTransfer
    ): QuoteTransfer;
}