<?php

namespace SprykerEco\Yves\Braintree\Processor;

use Generated\Shared\Transfer\PaypalExpressSuccessResponseTransfer;

interface PaypalResponseProcessorInterface
{
    /**
     * @param array $payload
     *
     * @return PaypalExpressSuccessResponseTransfer
     */
    public function successResponse(array $payload): PaypalExpressSuccessResponseTransfer;
}