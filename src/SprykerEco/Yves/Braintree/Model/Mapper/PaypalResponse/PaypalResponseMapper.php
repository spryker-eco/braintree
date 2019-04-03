<?php

namespace SprykerEco\Yves\Braintree\Mapper\PaypalResponse;

use Generated\Shared\Transfer\PaypalExpressSuccessResponseTransfer;

class PaypalResponseMapper implements PaypalResponseMapperInterface
{
    /**
     * @param array $payload
     *
     * @return PaypalExpressSuccessResponseTransfer
     */
    public function mapSuccessResponse(array $payload): PaypalExpressSuccessResponseTransfer
    {
        // TODO: Implement mapSuccessResponse() method.
    }
}