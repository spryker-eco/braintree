<?php

/**
 * MIT License
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace SprykerEco\Yves\Braintree\Model\QuoteExpander;

use Generated\Shared\Transfer\QuoteTransfer;
use Spryker\Yves\StepEngine\Dependency\Plugin\Handler\StepHandlerPluginInterface;
use SprykerEco\Yves\Braintree\Dependency\Client\BraintreeToCalculationClientInterface;
use SprykerEco\Yves\Braintree\Dependency\Client\BraintreeToQuoteClientInterface;
use Symfony\Component\HttpFoundation\Request;

class QuoteExpander implements QuoteExpanderInterface
{
    /**
     * @var \SprykerEco\Yves\Braintree\Dependency\Client\BraintreeToQuoteClientInterface
     */
    public $quoteClient;

    /**
     * @var \SprykerEco\Yves\Braintree\Dependency\Client\BraintreeToCalculationClientInterface
     */
    public $calculationClient;

    /**
     * @var \Spryker\Yves\StepEngine\Dependency\Plugin\Handler\StepHandlerPluginInterface
     */
    public $shipmentHandlerPlugin;

    /**
     * @param \SprykerEco\Yves\Braintree\Dependency\Client\BraintreeToQuoteClientInterface $quoteClient
     * @param \SprykerEco\Yves\Braintree\Dependency\Client\BraintreeToCalculationClientInterface $calculationClient
     * @param \Spryker\Yves\StepEngine\Dependency\Plugin\Handler\StepHandlerPluginInterface $shipmentHandlerPlugin
     */
    public function __construct(
        BraintreeToQuoteClientInterface $quoteClient,
        BraintreeToCalculationClientInterface $calculationClient,
        StepHandlerPluginInterface $shipmentHandlerPlugin
    ) {
        $this->quoteClient = $quoteClient;
        $this->calculationClient = $calculationClient;
        $this->shipmentHandlerPlugin = $shipmentHandlerPlugin;
    }

    /**
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @param int $idShipmentMethod
     *
     * @return \Generated\Shared\Transfer\QuoteTransfer
     */
    public function expandQuoteWithShipmentMethod(Request $request, int $idShipmentMethod): QuoteTransfer
    {
        $quoteClient = $this->quoteClient;

        $quoteTransfer = $quoteClient->getQuote();
        $quoteTransfer->getShipment()->setShipmentSelection((string)$idShipmentMethod);
        $quoteTransfer = $this->shipmentHandlerPlugin->addToDataClass($request, $quoteTransfer);
        $quoteTransfer = $this->calculationClient->recalculate($quoteTransfer);

        $quoteClient->setQuote($quoteTransfer);

        return $quoteTransfer;
    }
}
