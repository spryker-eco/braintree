<?php

namespace SprykerEco\Yves\Braintree\Mapper\PaypalResponse;

use Generated\Shared\Transfer\PaypalExpressSuccessResponseTransfer;

interface PaypalResponseMapperInterface
{
    /**
     * @param array $payload
     *
     * @return PaypalExpressSuccessResponseTransfer
     */
    public function mapSuccessResponse(array $payload): PaypalExpressSuccessResponseTransfer;
}