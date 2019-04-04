<?php

namespace SprykerEco\Yves\Braintree\Processor;

use Generated\Shared\Transfer\PaypalExpressSuccessResponseTransfer;
use SprykerEco\Yves\Braintree\Mapper\PaypalResponse\PaypalResponseMapperInterface;

class PaypalResponseProcessor implements PaypalResponseProcessorInterface
{
    /**
     * @var PaypalResponseMapperInterface
     */
    protected $paypalResponseMapper;

    /**
     * @param PaypalResponseMapperInterface $paypalResponseMapper
     */
    public function __construct(
        PaypalResponseMapperInterface $paypalResponseMapper
    ) {
        $this->paypalResponseMapper = $paypalResponseMapper;
    }

    /**
     * @param array $payload
     *
     * @return PaypalExpressSuccessResponseTransfer
     */
    public function successResponse(string $payload): PaypalExpressSuccessResponseTransfer
    {
        $responseTransfer = $this->paypalResponseMapper->mapSuccessResponse($payload);
    }
}