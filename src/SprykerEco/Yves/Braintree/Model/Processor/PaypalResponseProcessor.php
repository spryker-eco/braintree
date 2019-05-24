<?php

/**
 * MIT License
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace SprykerEco\Yves\Braintree\Model\Processor;

use Generated\Shared\Transfer\PaypalExpressSuccessResponseTransfer;
use Generated\Shared\Transfer\QuoteTransfer;
use SprykerEco\Yves\Braintree\Dependency\Client\BraintreeToQuoteClientInterface;
use SprykerEco\Yves\Braintree\Model\Mapper\PaypalResponse\PaypalResponseMapperInterface;

class PaypalResponseProcessor implements PaypalResponseProcessorInterface
{
    /**
     * @var \SprykerEco\Yves\Braintree\Model\Mapper\PaypalResponse\PaypalResponseMapperInterface
     */
    protected $paypalResponseMapper;

    /**
     * @var \SprykerEco\Yves\Braintree\Dependency\Client\BraintreeToQuoteClientInterface
     */
    protected $quoteClient;

    /**
     * @param \SprykerEco\Yves\Braintree\Model\Mapper\PaypalResponse\PaypalResponseMapperInterface $paypalResponseMapper
     * @param \SprykerEco\Yves\Braintree\Dependency\Client\BraintreeToQuoteClientInterface $quoteClient
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
     * @return \Generated\Shared\Transfer\QuoteTransfer
     */
    public function processSuccessResponse(array $payload): QuoteTransfer
    {
        $paypalExpressSuccessResponseTransfer = $this->paypalResponseMapper->mapSuccessResponseToPaypalExpressSuccessResponseTransfer($payload);
        $quoteTransfer = $this->updateQuoteDependOnResponse($paypalExpressSuccessResponseTransfer);

        return $quoteTransfer;
    }

    /**
     * @param \Generated\Shared\Transfer\PaypalExpressSuccessResponseTransfer $paypalExpressSuccessResponseTransfer
     *
     * @return \Generated\Shared\Transfer\QuoteTransfer
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
