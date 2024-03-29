<?php

/**
 * MIT License
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace SprykerEco\Yves\Braintree\Dependency\Client;

use Generated\Shared\Transfer\QuoteTransfer;
use Generated\Shared\Transfer\ShipmentMethodsTransfer;
use RuntimeException;

class BraintreeToShipmentClientBridge implements BraintreeToShipmentClientInterface
{
    /**
     * @var \Spryker\Client\Shipment\ShipmentClientInterface
     */
    protected $shipmentClient;

    /**
     * @param \Spryker\Client\Shipment\ShipmentClientInterface $shipmentClient
     */
    public function __construct($shipmentClient)
    {
        $this->shipmentClient = $shipmentClient;
    }

    /**
     * @param \Generated\Shared\Transfer\QuoteTransfer $quoteTransfer
     *
     * @throws \RuntimeException
     *
     * @return \Generated\Shared\Transfer\ShipmentMethodsTransfer
     */
    public function getAvailableMethods(QuoteTransfer $quoteTransfer): ShipmentMethodsTransfer
    {
        if (method_exists($this->shipmentClient, 'getAvailableMethodsByShipment') === true) {
            $shipmentMethodsCollectionTransfer = $this->shipmentClient->getAvailableMethodsByShipment($quoteTransfer);

            if ($shipmentMethodsCollectionTransfer->getShipmentMethods()->count() > 1) {
                throw new RuntimeException('Split shipping is not supported');
            }

            $shipmentMethodsTransfer = $shipmentMethodsCollectionTransfer->getShipmentMethods()->getIterator()
                ->current();

            return $shipmentMethodsTransfer;
        }

        return $this->shipmentClient->getAvailableMethods($quoteTransfer);
    }
}
