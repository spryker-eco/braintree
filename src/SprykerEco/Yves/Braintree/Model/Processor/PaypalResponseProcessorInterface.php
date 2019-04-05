<?php

namespace SprykerEco\Yves\Braintree\Model\Processor;

use Generated\Shared\Transfer\PaypalExpressSuccessResponseTransfer;
use Generated\Shared\Transfer\QuoteTransfer;

interface PaypalResponseProcessorInterface
{
    /**
     * @param array $payload
     *
     * @return QuoteTransfer
     */
    public function processSuccessResponse(array $payload): QuoteTransfer;
}