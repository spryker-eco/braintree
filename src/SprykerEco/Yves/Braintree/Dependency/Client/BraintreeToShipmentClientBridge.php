<?php

/**
 * MIT License
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace SprykerEco\Yves\Braintree\Dependency\Client;

use Generated\Shared\Transfer\QuoteTransfer;
use Generated\Shared\Transfer\ShipmentMethodsCollectionTransfer;
use Generated\Shared\Transfer\ShipmentMethodsTransfer;

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
     * @return \Generated\Shared\Transfer\ShipmentMethodsCollectionTransfer
     */
    public function getAvailableMethods(QuoteTransfer $quoteTransfer): ShipmentMethodsCollectionTransfer
    {
        return $this->shipmentClient->getAvailableMethodsByShipment($quoteTransfer);
    }
}
