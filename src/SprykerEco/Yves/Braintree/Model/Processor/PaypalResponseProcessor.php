<?php

namespace SprykerEco\Yves\Braintree\Model\Processor;

use Generated\Shared\Transfer\PaypalExpressSuccessResponseTransfer;
use Generated\Shared\Transfer\QuoteTransfer;
use SprykerEco\Yves\Braintree\Dependency\Client\BraintreeToQuoteClientInterface;
use SprykerEco\Yves\Braintree\Model\Mapper\PaypalResponse\PaypalResponseMapperInterface;

class PaypalResponseProcessor implements PaypalResponseProcessorInterface
{
    /**
     * @var PaypalResponseMapperInterface
     */
    protected $paypalResponseMapper;

    /**
     * @var BraintreeToQuoteClientInterface
     */
    protected $quoteClient;

    /**
     * @param PaypalResponseMapperInterface $paypalResponseMapper
     * @param BraintreeToQuoteClientInterface $quoteClient
     */
    public function __construct(
        PaypalResponseMapperInterface $paypalResponseMapper,
        BraintreeToQuoteClientInterface $quoteClient
    ) {
        $this->paypalResponseMapper = $paypalResponseMapper;
        $this->quoteClient = $quoteClient;
    }

    /**
     * @param array $payload
     *
     * @return QuoteTransfer
     */
    public function processSuccessResponse(array $payload): QuoteTransfer
    {
        $paypalExpressSuccessResponseTransfer = $this->paypalResponseMapper->mapSuccessResponseToPaypalExpressSuccessResponseTransfer($payload);
        $quoteTransfer = $this->updateQuoteDependOnResponse($paypalExpressSuccessResponseTransfer);

        return $quoteTransfer;
    }

    /**
     * @param PaypalExpressSuccessResponseTransfer $paypalExpressSuccessResponseTransfer
     *
     * @return QuoteTransfer
     */
    protected function updateQuoteDependOnResponse(PaypalExpressSuccessResponseTransfer $paypalExpressSuccessResponseTransfer): QuoteTransfer
    {
        $quoteTransfer = $this->quoteClient->getQuote();
        $quoteTransfer = $this->paypalResponseMapper->mapPaypalExpressSuccessResponseTransferToQuoteTransfer(
            $paypalExpressSuccessResponseTransfer,
            $quoteTransfer
        );

        $this->quoteClient->setQuote($quoteTransfer);

        return $quoteTransfer;
    }
}